<?php

namespace App\Tests\Security\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Personne;
use App\Tests\BaseTestCase;

class PersonneControllerTest extends BaseTestCase
{
    
    /*public function testInscriptionSucces(): void
    {
        $testData = [
            'pseudo' => 'john_doe',
            'email' => 'john@example.com',
            'nom' => 'Doe',
            'prenom' => 'John',
            'mdp' => 'password123',
            'confirmMdp' => 'password123',
        ];

        $this->client->request('POST', '/api/inscription', [
            'json' => $testData,
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['message' => 'Utilisateur inscrit avec succès!']);
    }*/

    /*public function testInscriptionEmailExistant(): void
    {
        $existingUser = [
            'pseudo' => 'john_doe',
            'email' => 'test@gmail.com',
            'nom' => 'Doe',
            'prenom' => 'John',
            'mdp' => 'password123',
            'confirmMdp' => 'password123',
        ];
        $this->client->request('POST', '/api/inscription', [
            'json' => $existingUser,
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);

        $this->assertResponseStatusCodeSame(409);
        $this->assertJsonContains(['error' => 'Cet email est déjà utilisé.']);
    }

    public function testMdpMismatch(): void
    {
        $existingUser = [
            'pseudo' => 'john_doe',
            'email' => 'test@gmail.com',
            'nom' => 'Doe',
            'prenom' => 'John',
            'mdp' => 'password125',
            'confirmMdp' => 'password123',
        ];
        $this->client->request('POST', '/api/inscription', [
            'json' => $existingUser,
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['error' => 'Mots de passes ne correspondent pas']);
    }

    public function testEmptyFields(): void
    {
        $existingUser = [
            'pseudo' => 'john_doe',
            'email' => 'test@gmail.com',
            'nom' => 'Doe',
            'prenom' => '',
            'mdp' => 'password125',
            'confirmMdp' => 'password123',
        ];
        $this->client->request('POST', '/api/inscription', [
            'json' => $existingUser,
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['error' => 'Remplir tous les champs!']);
    }*/
    
    public function testEditProfile(): void
    {
        $existingUser = [
            'email' => 'test@gmail.com',
            'nom' => 'Doe',
            'prenom' => 'John',
            'mdp' => 'password125',
            'confirmMdp' => 'password123',
        ];
        $this->client->request('PUT', '/api/editProfile/1', [
            'json' => $existingUser,
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['message' => 'Profile mise à jour!']);
    }

    public function testProfileNotFound(): void
    {
        $existingUser = [
            'email' => 'test@gmail.com',
            'nom' => 'Doe',
            'prenom' => 'John',
            'mdp' => 'password125',
            'confirmMdp' => 'password123',
        ];
        $this->client->request('PUT', '/api/editProfile/2', [
            'json' => $existingUser,
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testSupprimeCompte(): void
    {

        // Create a test user
        $testUser = new Personne();
        $testUser->setPseudo('delete_test');
        $testUser->setEmail('delete_test@example.com');
        $testUser->setNom('Delete');
        $testUser->setPrenom('Test');
        $testUser->setMdp('password123');

        $this->entityManager->persist($testUser);
        $this->entityManager->flush();

        $userId = $testUser->getId();

        $this->client->request('DELETE', '/api/supprimeCompte/' . $userId, [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $personne = $this->entityManager->getRepository(Personne::class)->findOneBy(['id' => $userId]);
        $this->assertEmpty($personne);
    }
}
