<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Entity\Personne;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Annotations as OA;

#[Route('api/reservation')]
final class ReservationController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/api/reservation/{id}",
     *     summary="Get details of a specific reservation",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the trip",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns trip details",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="nbrPlaces", type="integer"),
     *             @OA\Property(property="villeArrivee", type="string"),
     *             @OA\Property(property="villeDepart", type="string"),
     *             @OA\Property(property="dateTrajet", type="string", format="date-time"),
     *             @OA\Property(property="conducteur", type="object",
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="prenom", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Trip not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/{id}', name: 'app_reservation_detailsReservation', methods: ['GET'])]
    public function detailsReservation(Trajet $trajet, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$trajet) {
            return new JsonResponse(['error' => 'Trajet not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $trajetRecherche = [
            'id' => $trajet->getId(),
            'nbrPlaces' => $trajet->getNbrPlaces(),
            'villeArrivee' => $trajet->getVilleArrivee()->getLabel(),
            'villeDepart' => $trajet->getVilleDepart()->getLabel(),
            'dateTrajet' => $trajet->getDateTrajet(),
            'conducteur' => [
                'nom' => $trajet->getPersonne()->getNom(),
                'prenom' => $trajet->getPersonne()->getPrenom()
            ]
        ];

        return new JsonResponse($trajetRecherche, JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/reservation/listeReservations/{id}",
     *     summary="Get the list of reservations for a specific person",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the person",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns list of trips reserved by the person",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="conducteur", type="array", 
     *                     @OA\Items(type="string")
     *                 ),
     *                 @OA\Property(property="villeDepart", type="string"),
     *                 @OA\Property(property="villeArrivee", type="string"),
     *                 @OA\Property(property="nombrePlaces", type="integer"),
     *                 @OA\Property(property="date", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/listeReservations/{id}', name: 'app_reservation_listeReservations', methods: ['GET'])]
    public function listeReservations(Personne $personne): JsonResponse
    {
        $trajetsReserves =  $personne->getTrajetsReserves();

        $mesTrajetsReserves = [];
        foreach ($trajetsReserves as $trajet) {
            $mesTrajetsReserves[] = [
                'conducteur' => [$trajet->getPersonne()->getNom(), $trajet->getPersonne()->getPrenom()],
                'villeDepart' => $trajet->getVilleDepart()->getLabel(),
                'villeArrivee' => $trajet->getVilleArrivee()->getLabel(),
                'nombrePlaces' => $trajet->getNbrPlaces(),
                'date' => $trajet->getDateTrajet()->format('Y-m-d H:i')
            ];
        }
        return new JsonResponse($mesTrajetsReserves, JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/reservation/{idPers}/{idTrajet}",
     *     summary="Reserve a spot on a trip for a person",
     *     @OA\Parameter(
     *         name="idPers",
     *         in="path",
     *         description="Person ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idTrajet",
     *         in="path",
     *         description="Trip ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No available seats",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/{idPers}/{idTrajet}', name: 'app_reservation_reserverTrajet', methods: ['POST'])]
    public function reserverTrajet(Request $request, EntityManagerInterface $entityManager, $idPers, $idTrajet): JsonResponse
    {
        $passager = $entityManager->getRepository(Personne::class)->findOneBy(['id' => $idPers]);
        $trajet = $entityManager->getRepository(Trajet::class)->findOneBy(['id' => $idTrajet]);

        $nbrPlaces = $trajet->getNbrPlaces();
        if ($nbrPlaces > 0) {
            $trajet->addPassager($passager);
            $trajet->setNbrPlaces(($trajet->getNbrPlaces() - 1));
            $entityManager->persist($trajet);
            $entityManager->flush();
            return new JsonResponse(["message" => "Reservation effectué!"], JsonResponse::HTTP_OK);
        } else {
            return new JsonResponse(["status" => "error", "message" => "Plus de place"], JsonResponse::HTTP_OK);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/reservation/annuleReservation/{idPers}/{idTrajet}",
     *     summary="Cancel a reservation for a trip",
     *     @OA\Parameter(
     *         name="idPers",
     *         in="path",
     *         description="Person ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="idTrajet",
     *         in="path",
     *         description="Trip ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation cancelled",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reservation or trip not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/annuleReservation/{idPers}/{idTrajet}', name: 'app_reservation_annuleReservation', methods: ['DELETE'])]
    public function deleteReservation(Request $request, EntityManagerInterface $entityManager, $idPers, $idTrajet): JsonResponse
    {
        $personne = $entityManager->getRepository(Personne::class)->findOneBy(['id' => $idPers]);
        $trajet = $entityManager->getRepository(Trajet::class)->findOneBy(['id' => $idTrajet]);
        if (!$trajet) {
            return new JsonResponse(['error' => 'Trajet pas trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }
        if (!$personne->getTrajetsReserves()->contains($trajet)) {
            return new JsonResponse(['error' => 'Reservation pas trouvé'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $trajet->getPersonne()->removeTrajetsReserves($trajet);
        $trajet->setNbrPlaces($trajet->getNbrPlaces() + 1);

        $entityManager->persist($trajet);
        $entityManager->persist($personne);
        $entityManager->flush();
        return new JsonResponse(['status' => 'OK', 'message' => 'Reservation annulé.'], JsonResponse::HTTP_OK);
    }
}
