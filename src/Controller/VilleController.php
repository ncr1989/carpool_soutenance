<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[Route('api/ville')]
final class VilleController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/api/ville/listeVilles",
     *     summary="Get list of all cities",
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of cities",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="nom", type="string"),
     *                 @OA\Property(property="city_code", type="string"),
     *                 @OA\Property(property="department", type="string"),
     *                 @OA\Property(property="department_number", type="string"),
     *                 @OA\Property(property="region", type="string")
     *             )
     *         )
     *     )
     * )
     */
    #[Route(path: '/listeVilles', name: 'app_ville_listeVilles', methods: ['GET'])]
    public function index(VilleRepository $villeRepository): JsonResponse
    {
        $villes = $villeRepository->findAll();
        $villesArray = array_map(function ($ville) {
            return [
                //'id' => $ville->getId(),
                'nom' => $ville->getLabel(),
                //'city_code' => $ville->getCityCode(),
                //'department' => $ville->getDepartmentName(),
                //'department_number' => $ville->getDepartmentNumber(),
                //'region' => $ville->getRegionName(),
            ];
        }, $villes);
        return new JsonResponse($villesArray, JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/ville/listeCP",
     *     summary="Get list of postal codes",
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of postal codes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="zip_code", type="string")
     *             )
     *         )
     *     )
     * )
     */
    #[Route(path: '/listeCP', name: 'app_ville_listeCP', methods: ['GET'])]
    public function listeCps(VilleRepository $villeRepository): JsonResponse
    {
        $cps = $villeRepository->findAll();
        $cpArray = array_map(function ($ville) {
            return ['zip_code' => $ville->getZipCode()];
        }, $cps);
        return new JsonResponse($cpArray, JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/ville/new",
     *     summary="Create a new city",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="ville", type="string"),
     *             @OA\Property(property="cp", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="City created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/new', name: 'app_ville_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ville = new Ville();
        $nom = $data['ville'];
        $cp = $data['cp'];
        $insee_code = $data['insee_code'];
        $zip_code = $data['zip_code'];
        $label = $data['label'];
        $department_name = $data['department_name'];
        $department_number = $data['department_number'];
        $region_name = $data['region_name'];
        $region_geojson_name = $data['region_geojson_name'];
        
        

        $ville->setCityCode($cp);
        $ville->setLabel($nom);
        $ville->setInseeCode($insee_code);
        $ville->setZipCode($zip_code);
        $ville->setDepartmentName($department_name);
        $ville->setDepartmentNumber($department_number);
        $ville->setRegionName($region_name);
        $ville->setRegionGeojsonName($region_geojson_name);
        
        
        
        

        $entityManager->persist($ville);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Ville crée avec succès!'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/ville/{id}",
     *     summary="Delete a city",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="City ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="Succés", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/{id}', name: 'app_ville_delete', methods: ['DELETE'])]
    public function supprimeVille(EntityManagerInterface $entityManager, Ville $ville): JsonResponse
    {
        $entityManager->remove($ville);
        $entityManager->flush();

        return new JsonResponse(['Succés' => 'Ville supprimée.'], JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/ville/{id}/edit",
     *     summary="Edit a city",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="City ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="ville", type="string"),
     *             @OA\Property(property="cp", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="City updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
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
}
