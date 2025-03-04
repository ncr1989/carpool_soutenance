<?php

// tests/Functional/LoginControllerTest.php
namespace App\Tests\Security\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\BaseTestCase;

class LoginControllerTest extends BaseTestCase
{
    public function testLoginSuccess(): void
    {
        $client = static::createClient();
        $response = $client->request('POST', '/api/login', [
            'json' => [
                'email' => 'test@gmail.com',
                'mdp' => 'adminpass',
            ],
        ]);

        $this->assertResponseStatusCodeSame(200);
        $data = $response->toArray();

        $this->assertArrayHasKey('token', $data);
        $this->assertIsString($data['token']);

        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
        

        $token = $data['token'];


        $client->request('GET', '/api/trajet/listeTrajets', [
            'headers' => [
                'x-auth-token' => $token,
            ],
        ]);

        // Step 5: Assert the request was successful (assuming 200 OK for valid token)
        $this->assertResponseStatusCodeSame(200);
    }

    public function testLoginBadRequestMissingEmailOrPassword(): void
    {
        $client = static::createClient();

        // Test with missing email
        $response = $client->request('POST', '/api/login', [
            'json' => [
                'mdp' => 'password123',
            ],
        ]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'error' => 'Email et mdp obligatoire',
        ]);

        // Test with missing password
        $response = $client->request('POST', '/api/login', [
            'json' => [
                'email' => 'user@example.com',
            ],
        ]);
        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'error' => 'Email et mdp obligatoire',
        ]);
    }

    public function testLoginForbiddenInvalidEmail(): void
    {
        $client = static::createClient();

        // Test with a non-existent email
        $response = $client->request('POST', '/api/login', [
            'json' => [
                'email' => 'nonexistent@example.com',
                'mdp' => 'password123',
            ],
        ]);
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'error' => 'Mdp ou email invalide.',
        ]);
    }

    /*public function testLoginUnauthorizedInvalidPassword(): void
    {
        $client = static::createClient();

        // Test with incorrect password
        $response = $client->request('POST', '/api/login', [
            'json' => [
                'email' => 'test@gmail.com',
                'mdp' => 'wrongpassword',
            ],
        ]);
        var_dump($response->getStatusCode());
        $content = $response->getContent();  // Get raw response content
        var_dump($content); // Output raw response content

        // Decode the response manually
        $data = json_decode($content, true);  // Decode the JSON response into an array

        // Check the response status code (401)
        $this->assertResponseStatusCodeSame(401);

        // Ensure the error message is present in the response
        $this->assertArrayHasKey('error', $data);
        $this->assertIsString($data['error']);
        $this->assertEquals('Invalid email or password.', $data['error']);
    }*/
}
