<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Annotations as OA;

#[Route('/api')]
final class PersonneController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/api",
     *     summary="List all persons",
     *     @OA\Response(
     *         response=200,
     *         description="Returns a list of all persons",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    #[Route(name: 'app_personne_index', methods: ['GET'])]
    public function index(PersonneRepository $personneRepository): Response
    {
        return $this->render('personne/index.html.twig', [
            'personnes' => $personneRepository->findAll(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/inscription",
     *     summary="User registration",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="pseudo", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="prenom", type="string"),
     *             @OA\Property(property="mdp", type="string"),
     *             @OA\Property(property="confirmMdp", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict: Email already used",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/inscription', name: 'new_inscription', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $pseudo = $data['pseudo'];
        $email = $data['email'];
        $nom =  $data['nom'];
        $prenom = $data['prenom'];
        $mdp = $data['mdp'];
        $confirmMdp = $data['confirmMdp'];

        if (empty($email) || empty($mdp) || empty($confirmMdp)|| empty($pseudo)|| empty($nom) || empty($prenom)) {
            return new JsonResponse(['error' => 'Remplir tous les champs!'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!hash_equals($mdp, $confirmMdp)) {
            return new JsonResponse(['error' => 'Mots de passes ne correspondent pas'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingUser = $entityManager->getRepository(Personne::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Cet email est déjà utilisé.'], JsonResponse::HTTP_CONFLICT);
        }

        $personne = new Personne();
        $hashedMdp = password_hash($mdp, PASSWORD_DEFAULT);
        $personne->setEmail($email);
        $personne->setMdp($hashedMdp);
        $personne->setPseudo($pseudo);
        $personne->setNom($nom);
        $personne->setPrenom($prenom);

        $entityManager->persist($personne);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur inscrit avec succès!'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/editProfile/{id}",
     *     summary="Update user profile",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="prenom", type="string"),
     *             @OA\Property(property="nom", type="string"),
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="tel", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid JSON or missing parameters",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/editProfile/{id}', name: 'app_personne_editCompte', methods: ['PUT'])]
    public function edit(Request $request, Personne $personne, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => ' JSON'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!empty($data['nom'])) {
            $personne->setNom($data['nom']);
        }
        if (!empty($data['prenom'])) {
            $personne->setPrenom($data['prenom']);
        }
        if (!empty($data['email'])) {
            $personne->setEmail($data['email']);
        }
        if (!empty($data['tel'])) {
            $personne->setTel($data['tel']);
        }

        $entityManager->persist($personne);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Profile mise à jour!'], JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/supprimeCompte/{id}",
     *     summary="Delete a user account",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Account deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="Succés", type="string")
     *         )
     *     )
     * )
     */
    #[Route('/supprimeCompte/{id}', name: 'app_personne_supprimeCompte', methods: ['DELETE'])]
    public function delete(Request $request, Personne $personne, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($personne);
        $entityManager->flush();

        return new JsonResponse(['Succés' => 'Compte supprime.'], JsonResponse::HTTP_OK);
    }
}
