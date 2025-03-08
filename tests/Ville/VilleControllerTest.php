<?php

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Personne;
use App\Tests\BaseTestCase;
use Doctrine\DBAL\Types\Types;

class VilleControllerTest extends BaseTestCase
{
    public function testListeVilles(): void
    {
        $response =  $this->client->request('GET', '/api/ville/listeVilles', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $data = $response->toArray();
        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayHasKey('nom', $data[0]);
        $this->assertArrayHasKey('city_code', $data[0]);
        $this->assertArrayHasKey('department', $data[0]);
        $this->assertArrayHasKey('department_number', $data[0]);
        $this->assertArrayHasKey('region', $data[0]);
        $this->assertEquals(count($data), 324);
    }

    public function testListeCps(): void
    {
        $response =  $this->client->request('GET', '/api/ville/listeCP', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $data = $response->toArray();

        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayHasKey('zip_code', $data[0]);
        $this->assertEquals(count($data), 324);
    }

    public function testNewVille(): void
    {
        $response =  $this->client->request('POST', '/api/ville/new', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
    }
}
