<?php 

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

abstract class BaseTestCase extends ApiTestCase
{
    protected $client;
    protected $entityManager;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        
        $response = $this->client->request('POST', '/api/login', [
            'json' => [
                'email' => 'test@gmail.com',
                'mdp' => 'adminpass',
            ],
        ]);
        
        $data = $response->toArray();
        $this->token = $data['token'];
    }
}