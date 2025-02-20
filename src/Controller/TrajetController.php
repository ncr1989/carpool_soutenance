<?php

namespace App\Controller;

use App\Entity\Trajet;
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

#[Route('api/trajet')]
final class TrajetController extends AbstractController
{
    #[Route('/all', name: 'app_trajet_index', methods: ['GET'])]
    public function listeTrajets(TrajetRepository $trajetRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $trajets = $trajetRepository->findAll();

        $trajetsArray = array_map(function ($trajet) {
            return [
                'id' => $trajet->getId(),
                'nbrPlaces' => $trajet->getNbrPlaces(),
                'villeArrivee' => $trajet->getVilleArrivee()->getLabel(),
                'villeDepart' => $trajet->getVilleDepart()->getLabel(),
                'dateTrajet' => $trajet->getDateTrajet(),
                'conducteur' => [$trajet->getPersonne()->getNom(), $trajet->getPersonne()->getPrenom()]



            ];
        }, $trajets);

        return new JsonResponse($trajetsArray, JsonResponse::HTTP_OK);
    }

    //create trajet
    #[Route('/new', name: 'app_trajet_new', methods: ['POST'])]
    public function nouveauTrajet(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $data = json_decode($request->getContent(), true);
        $villeD = $data['villeDepart'];
        $villeA = $data['villeArrivee'];
        $personneId = $data['personneId'];
        $nbrPlaces = (int)$data['nbrPlaces'];
        $dateTrajet = $data['dateTrajet'];
        $conducteur = filter_var($data['conducteur'], FILTER_VALIDATE_BOOLEAN);

        //if(empty($villeD) || empty($villeA) || empty($personneId)|| empty($nbrPlaces) || empty($dateTrajet) || $conducteur == null){
        //    return new JsonResponse(['error' => 'Remplir tous les champs!'], JsonResponse::HTTP_BAD_REQUEST); 
        //}
        $personne = $entityManager->getRepository(Personne::class)->find($personneId);
        $villeDepart = $entityManager->getRepository(Ville::class)->findOneBy(['label' => $villeD]);
        $villeArrivee = $entityManager->getRepository(Ville::class)->findOneBy(['label' => $villeA]);

        $dateTrajet = \DateTime::createFromFormat('Y-m-d\TH:i:s', $dateTrajet);
        $trajet = new Trajet();
        $trajet->setVilleArrivee($villeArrivee);
        $trajet->setVilleDepart($villeDepart);
        $trajet->setNbrPlaces($nbrPlaces);
        $trajet->setDateTrajet($dateTrajet);
        $trajet->setConducteur($conducteur);
        $trajet->setPersonne($personne);
        $entityManager->persist($trajet);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Trajet inscrit avec succès!'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_trajet_detailsTrajet', methods: ['GET'])]
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
                'dateTrajet' => $trajet->getDateTrajet()->format('Y-m-d H:i:s'), // Formatting date
                'conducteur' => [
                    'nom' => $trajet->getPersonne()->getNom(),
                    'prenom' => $trajet->getPersonne()->getPrenom()
                ]
            ];
        }
        return new JsonResponse($trajetArray, JsonResponse::HTTP_OK);
    }


    #[Route('/{id}/reserver', name: 'app_trajet_reserverTrajet', methods: ['POST'])]
    public function reserverTrajet(Request $request, Trajet $trajet, EntityManagerInterface $entityManager): Response
    {
        $nbrPlaces = $trajet->getNbrPlaces();
        if ($nbrPlaces > 0) {
            $trajet->addPassager($trajet->getPersonne());
            $trajet->setNbrPlaces(($trajet->getNbrPlaces() - 1));
            $entityManager->persist($trajet);
            $entityManager->flush();
            return new JsonResponse(["Success" => "Reservation effectué!"], JsonResponse::HTTP_OK);
        } else {
            return new JsonResponse(["Rservation impossiible" => "Plus de place"], JsonResponse::HTTP_OK);
        }
    }

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


    #[Route('/annuleReservation/{id}', name: 'app_trajet_annuleReservation', methods: ['DELETE'])]
    public function deleteReservation(Request $request, Trajet $trajet, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$trajet) {
            return new JsonResponse(['error' => 'Trajet not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $trajet->getPersonne()->removeTrajetsReserves($trajet);
        $entityManager->persist($trajet);
        $entityManager->flush();
        return new JsonResponse(['Succés' => 'Reservation annulé.'], JsonResponse::HTTP_OK);
    }
    #[Route('/supprimeTrajet/{id}', name: 'app_trajet_supprimeTrajet', methods: ['DELETE'])]
    public function deleteTrajetProposes(Request $request, Trajet $trajet, EntityManagerInterface $entityManager): JsonResponse
    {
        //$trajet->getPersonne()->removeTrajetsPropose($trajet);
        $entityManager->remove($trajet);
        $entityManager->flush();

        return new JsonResponse(['Succés' => 'Trajet supprime.'], JsonResponse::HTTP_OK);
    }
}
