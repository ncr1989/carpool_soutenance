<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Service\Mailer;
use App\Entity\Personne;
use App\Entity\Ville;
use App\Form\TrajetType;
use App\Repository\TrajetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[Route('api/trajet')]
final class TrajetController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/api/trajet/listeTrajets",
     *     summary="Get list of all trips",
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of trips",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nbrPlaces", type="integer"),
     *                 @OA\Property(property="villeArrivee", type="string"),
     *                 @OA\Property(property="villeDepart", type="string"),
     *                 @OA\Property(property="dateTrajet", type="string", format="date-time"),
     *                 @OA\Property(property="conducteur", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    #[Route('/listeTrajets', name: 'app_trajet_index', methods: ['GET'])]
    public function listeTrajets(TrajetRepository $trajetRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $trajets = $trajetRepository->findAll();
        $trajetsArray = array_map(function ($trajet) {
            return [
                'id' => $trajet->getId(),
                'nbrPlaces' => $trajet->getNbrPlaces(),
                'villeArrivee' => $trajet->getVilleArrivee()->getLabel(),
                'villeDepart' => $trajet->getVilleDepart()->getLabel(),
                'dateTrajet' => $trajet->getDateTrajet()->format('Y-m-d'),
                'heureTrajet'=> $trajet->getDateTrajet()->format('H:i'),
                'conducteur' => [$trajet->getPersonne()->getNom(), $trajet->getPersonne()->getPrenom()]
            ];
        }, $trajets);
        return new JsonResponse($trajetsArray, JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/trajet/new",
     *     summary="Create a new trip",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="villeDepart", type="string"),
     *             @OA\Property(property="villeArrivee", type="string"),
     *             @OA\Property(property="personneId", type="integer"),
     *             @OA\Property(property="nbrPlaces", type="integer"),
     *             @OA\Property(property="dateTrajet", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Trip created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/new', name: 'app_trajet_new', methods: ['POST'])]
    public function nouveauTrajet(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $villeD = $data['villeDepart'];
        $villeA = $data['villeArrivee'];
        $personneId = $data['personneId'];
        $nbrPlaces = (int)$data['nbrPlaces'];
        $dateTrajet = $data['dateTrajet'];

        if (empty($villeD) || empty($villeA) || empty($personneId) || empty($nbrPlaces) || empty($dateTrajet)) {
            return new JsonResponse(['error' => 'Remplir tous les champs!'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $personne = $entityManager->getRepository(Personne::class)->find($personneId);
        $villeDepart = $entityManager->getRepository(Ville::class)->findOneBy(['label' => $villeD]);
        $villeArrivee = $entityManager->getRepository(Ville::class)->findOneBy(['label' => $villeA]);

        $dateTrajet = \DateTime::createFromFormat('Y-m-d\TH:i:s', $dateTrajet);
        $trajet = new Trajet();
        $trajet->setVilleArrivee($villeArrivee);
        $trajet->setVilleDepart($villeDepart);
        $trajet->setNbrPlaces($nbrPlaces);
        $trajet->setDateTrajet($dateTrajet);
        $trajet->setPersonne($personne);
        $entityManager->persist($trajet);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Trajet inscrit avec succès!'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/trajet/{villeD}/{villeA}/{dateTrajet}",
     *     summary="Search for trips based on cities and date",
     *     @OA\Parameter(
     *         name="villeD",
     *         in="path",
     *         description="Departure city",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="villeA",
     *         in="path",
     *         description="Arrival city",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="dateTrajet",
     *         in="path",
     *         description="Date of the trip",
     *         required=true,
     *         @OA\Schema(type="string", format="date-time")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of trips",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nbrPlaces", type="integer"),
     *                 @OA\Property(property="villeArrivee", type="string"),
     *                 @OA\Property(property="villeDepart", type="string"),
     *                 @OA\Property(property="dateTrajet", type="string", format="date-time"),
     *                 @OA\Property(property="conducteur", type="object",
     *                     @OA\Property(property="nom", type="string"),
     *                     @OA\Property(property="prenom", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="City not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/{villeD}/{villeA}/{dateTrajet}', name: 'app_trajet_rechercheTrajet', methods: ['GET'])]
    public function rechercheTrajet(EntityManagerInterface $entityManager, $dateTrajet, $villeA, $villeD): JsonResponse
    {
        $villeDepart = $entityManager->getRepository(Ville::class)->findOneBy(['label' => $villeD]);
        $villeArrivee = $entityManager->getRepository(Ville::class)->findOneBy(['label' => $villeA]);

        if (!$villeDepart || !$villeArrivee) {
            return new JsonResponse(['error' => 'Ville not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        try {
            $date = new \DateTime($dateTrajet);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $trajets = $entityManager->getRepository(Trajet::class)
            ->createQueryBuilder('t')
            ->where('t.villeDepart = :villeDepart')
            ->andWhere('t.villeArrivee = :villeArrivee')
            ->andWhere('t.dateTrajet >= :dateTrajet')  
            ->setParameter('villeDepart', $villeDepart)
            ->setParameter('villeArrivee', $villeArrivee)
            ->setParameter('dateTrajet', $date)
            ->getQuery()
            ->getResult();

        // Transform results to array
        $trajetArray = [];
        foreach ($trajets as $trajet) {
            $trajetArray[] = [
                'id' => $trajet->getId(),
                'nbrPlaces' => $trajet->getNbrPlaces(),
                'villeArrivee' => $trajet->getVilleArrivee()->getLabel(),
                'villeDepart' => $trajet->getVilleDepart()->getLabel(),
                'dateTrajet' => $trajet->getDateTrajet()->format('Y-m-d '),
                'heureTrajet' => $trajet->getDateTrajet()->format('H:i'),
                'conducteur' => [
                    'nom' => $trajet->getPersonne()->getNom(),
                    'prenom' => $trajet->getPersonne()->getPrenom()
                ]
            ];
        }
        return new JsonResponse($trajetArray, JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/trajet/listePassagers/{id}",
     *     summary="Get list of passengers for a specific trip",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Trip ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of passengers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="prenom", type="string"),
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="telephone", type="string")
     *             )
     *         )
     *     )pubcl
     * )
     */
    #[Route('/listePassagers/{id}', name: 'app_trajet_listePassagers', methods: ['GET'])]
    public function listePassagers(Request $request, Trajet $trajet, EntityManagerInterface $entityManager): Response
    {
        $passagers =  $trajet->getPassagers();
        $passagersArray = [];
        foreach ($passagers as $passager) {
            $passagersArray[] = [
                'id' => $passager->getId(),
                'prenom' => $passager->getPrenom(),
                'nom' => $passager->getNom(),
                'email' => $passager->getEmail(),
                'telephone' => $passager->getTel()
            ];
        }
        return new JsonResponse($passagersArray, JsonResponse::HTTP_OK);
    }


    #[Route('/listeTrajetsProposes/{id}', name: 'app_trajet_listeTrajetsProposes', methods: ['GET'])]
    public function listeTrajetsProposes(Personne $personne): JsonResponse
    {
        
        $trajetsProposes =  $personne->getTrajetsProposes();

        $mesTrajetsProposes = [];
        foreach ($trajetsProposes as $trajet) {
            $mesTrajetsProposes[] = [
                'id'=> $trajet->getId(),
                'conducteur' => [ "nom" => $trajet->getPersonne()->getNom(),"prenom" => $trajet->getPersonne()->getPrenom()],
                'villeDepart' => $trajet->getVilleDepart()->getLabel(),
                'villeArrivee' => $trajet->getVilleArrivee()->getLabel(),
                'nombrePlaces' => $trajet->getNbrPlaces(),
                'dateTrajet' => $trajet->getDateTrajet()->format('Y-m-d'),
                'heureTrajet'=> $trajet->getDateTrajet()->format('H:i'),
            ];
        }
        return new JsonResponse($mesTrajetsProposes, JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/trajet/{id}",
     *     summary="Delete a trip",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Trip ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Trip deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="Succés", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/{id}', name: 'app_trajet_supprimeTrajet', methods: ['DELETE'])]
    public function deleteTrajetProposes(Request $request, Trajet $trajet, EntityManagerInterface $entityManager,Mailer $mailer): JsonResponse
    {

        
        $entityManager->remove($trajet);
        //$mailer->sendEmail( $trajet);
        $entityManager->flush();
        return new JsonResponse(['Succés' => 'Trajet supprime.'], JsonResponse::HTTP_OK);
       
    }


    #[Route('/{id}', name: 'app_reservation_detailsTrajet', methods: ['GET'])]
    public function detailsTrajet(Trajet $trajet, EntityManagerInterface $entityManager): JsonResponse
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
}
