<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Personne;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Doctrine\Persistence\ManagerRegistry;


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

    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data= json_decode($request->getContent(), true);
        $email = $data['email']?? null;
        $mdp = $data['mdp']?? null;

        if(!$email || !$mdp){
            return new JsonResponse(['error' => 'Email et mdp obligatoire'], JsonResponse::HTTP_BAD_REQUEST); 
        }
        $user = $this->doctrine->getRepository(Personne::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return new JsonResponse(['error' => 'Mdp ou email invlaide.'], JsonResponse::HTTP_UNAUTHORIZED);
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
