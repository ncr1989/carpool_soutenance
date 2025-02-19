<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('api/ville')]
final class VilleController extends AbstractController
{
    #[Route(name: 'app_ville_index', methods: ['GET'])]
    public function index(VilleRepository $villeRepository): JsonResponse
    {
        $villes = $villeRepository->findAll();
       // Map each Ville object to an array
    $villesArray = array_map(function($ville) {
        return [
            'id' => $ville->getId(),
            'name' => $ville->getNom(à), // Replace 'name' with the actual property name in your Ville class
        ];
    }, $villes);

    return new JsonResponse($villesArray, JsonResponse::HTTP_OK);
    }

    #[Route('/new', name: 'app_ville_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data= json_decode($request->getContent(), true);
        $nom = $data['ville'];
        $cp = $data['cp'];
        $ville = new Ville();
        $ville->setCodePostal($cp);
        $ville->setNom($nom);
        $entityManager->persist($ville);
        $entityManager->flush();
    
        return new JsonResponse(['message' => 'Ville cree avec succès!'], JsonResponse::HTTP_CREATED);
        
    }

    #[Route('/{id}', name: 'app_ville_show', methods: ['GET'])]
    public function show(Ville $ville): Response
    {
        return $this->render('ville/show.html.twig', [
            'ville' => $ville,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ville_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ville $ville, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ville/edit.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ville_delete', methods: ['DELETE'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data= json_decode($request->getContent(), true);
    

        return new JsonResponse(['message' => 'Ville cree avec succès!'], JsonResponse::HTTP_CREATED);
    }
}
