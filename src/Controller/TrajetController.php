<?php

namespace App\Controller;

use App\Entity\Trajet;
use App\Entity\Personne;
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
    #[Route( '/all',name: 'app_trajet_index', methods: ['GET'])]
    public function index(TrajetRepository $trajetRepository): JsonResponse
    {
        $trajets = $trajetRepository->findAll();
    $trajetsArray = array_map(function($trajet) {
        return [
            'id' => $trajet->getId(),
            'nbrPlaces' => $trajet->getNbrPlaces(), 
        ];
    }, $trajets);

    return new JsonResponse($trajetsArray, JsonResponse::HTTP_OK);
    }

    #[Route('/new', name: 'app_trajet_new', methods: [ 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $data= json_decode($request->getContent(), true);
        $villeD = $data['villeDepart'];
        $villeA = $data['villeArrivee'];
        $personneId =$data['personneId'];
        $nbrPlaces =(int)$data['nbrPlaces'];
        $dateTrajet =$data['dateTrajet'];
        $conducteur =filter_var($data['conducteur'], FILTER_VALIDATE_BOOLEAN);

        $villeId = 1;

        //if(empty($villeD) || empty($villeA) || empty($personneId)|| empty($nbrPlaces) || empty($dateTrajet) || $conducteur == null){
        //    return new JsonResponse(['error' => 'Remplir tous les champs!'], JsonResponse::HTTP_BAD_REQUEST); 
        //}
        $personne = $entityManager->getRepository(Personne::class)->find($personneId);
        $ville = $entityManager->getRepository(Ville::class)->find($villeId);

        $dateTrajet = \DateTime::createFromFormat('Y-m-d\TH:i:s', $dateTrajet);
        $trajet = new Trajet();
        $trajet->setVilleA($villeA);
        $trajet->setVilleD($villeD);
        $trajet->setNbrPlaces($nbrPlaces);
        $trajet->setDateTrajet($dateTrajet);
        $trajet->setConducteur($conducteur);
        $trajet->setPersonne($personne);
        $trajet->setTrajet($ville);
        $entityManager->persist($trajet);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Trajet inscrit avec succès!'], JsonResponse::HTTP_CREATED);
    }

   /* #[Route('/{id}', name: 'app_trajet_show', methods: ['GET'])]
    public function show(Trajet $trajet): JsonResponse
    {
        $data 
    }*/

    #[Route('/{id}/edit', name: 'app_trajet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trajet $trajet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrajetType::class, $trajet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_trajet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trajet/edit.html.twig', [
            'trajet' => $trajet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trajet_delete', methods: ['POST'])]
    public function delete(Request $request, Trajet $trajet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trajet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trajet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_trajet_index', [], Response::HTTP_SEE_OTHER);
    }
}
