<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/personne')]
final class PersonneController extends AbstractController
{
    #[Route(name: 'app_personne_index', methods: ['GET'])]
    public function index(PersonneRepository $personneRepository): Response
    {
        return $this->render('personne/index.html.twig', [
            'personnes' => $personneRepository->findAll(),
        ]);
    }

    #[Route('/api/inscription', name: 'new_inscription', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $data= json_decode($request->getContent(), true);
        $email = $data['email'];
        $mdp = $data['mdp'];
        $confirmMdp = $data['confirmMdp'];

        if(empty($email) || empty($mdp) || empty($confirmMdp)){
            return new JsonResponse(['error' => 'Remplir tous les champs!'], JsonResponse::HTTP_BAD_REQUEST); 
        }
        if (!hash_equals($mdp, $confirmMdp)) {
            return new JsonResponse(['error' => 'Mots de passes '], JsonResponse::HTTP_BAD_REQUEST);
        }
        
        $existingUser = $entityManager->getRepository(Personne::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Cet email est déjà utilisé.'], JsonResponse::HTTP_CONFLICT);
        }
    
        $personne = new Personne();
        $hashedMdp = password_hash($mdp, PASSWORD_DEFAULT);
        $personne->setEmail($email);
        $personne->setMdp($hashedMdp);
    
        $entityManager->persist($personne);
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Utilisateur inscrit avec succès!'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_personne_show', methods: ['GET'])]
    public function show(Personne $personne): Response
    {
        return $this->render('personne/show.html.twig', [
            'personne' => $personne,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_personne_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Personne $personne, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_personne_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('personne/edit.html.twig', [
            'personne' => $personne,
            'form' => $form,
        ]);
    }
    /*
    #[Route('/{id}', name: 'app_personne_delete', methods: ['DELETE'])]
    public function delete(Request $request, Personne $personne, EntityManagerInterface $entityManager): JsonResponse
    {
        

        return 
    }*/
}
