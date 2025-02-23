<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Personne;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use OpenApi\Annotations as OA;

final class LoginController extends AbstractController
{
    private $passwordHasher;
    private $jwtManager;
    private $doctrine;

    public function __construct(UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager, ManagerRegistry $doctrine )
    {
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
        $this->doctrine = $doctrine;
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login and JWT token generation",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="mdp", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login, returns a JWT token",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="token", type="string", description="JWT token"),
     *             @OA\Property(property="id", type="integer", description="User ID")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request, missing email or password",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Email et mdp obligatoire")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden, invalid email",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Mdp ou email invalide.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized, invalid password",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Invalid email or password.")
     *         )
     *     )
     * )
     */
    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $mdp = $data['mdp'] ?? null;

        if (!$email || !$mdp) {
            return new JsonResponse(['error' => 'Email et mdp obligatoire'], JsonResponse::HTTP_BAD_REQUEST); 
        }

        $user = $this->doctrine->getRepository(Personne::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['error' => 'Mdp ou email invalide.'], JsonResponse::HTTP_FORBIDDEN);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $mdp)) {
            return new JsonResponse(['error' => 'Invalid email or password.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($user);
        return new JsonResponse([
            'token' => $token,
            'id' => $user->getId(),
        ]);
    }
}
