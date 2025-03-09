<?php

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Personne;
use App\Entity\Ville;
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
        $allCities = $this->entityManager->getRepository(Ville::class)->findAll();
        $data = $response->toArray();
        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayHasKey('nom', $data[0]);
        $this->assertArrayHasKey('city_code', $data[0]);
        $this->assertArrayHasKey('department', $data[0]);
        $this->assertArrayHasKey('department_number', $data[0]);
        $this->assertArrayHasKey('region', $data[0]);
        $this->assertEquals(count($data), count($allCities));
    }

    public function testListeCps(): void
    {
        $response =  $this->client->request('GET', '/api/ville/listeCP', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $allCities = $this->entityManager->getRepository(Ville::class)->findAll();
        $data = $response->toArray();

        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayHasKey('zip_code', $data[0]);
        $this->assertEquals(count($data),count($allCities));
    }

    public function testNewVille(): void
    {
        $newVille = [
            "ville" =>"Paris",
            "cp" =>"7200",
            "insee_code"=>"42300",
            "zip_code"=>"42300",
            "label"=>"Paris",
            "department_name"=>"test",
            "department_number"=>1256,
            "region_name"=>"test2",
            "region_geojson_name"=>"test8",
            
            
            

            
            
            
        ];
        $response =  $this->client->request('POST', '/api/ville/new', [
            'json' => $newVille,
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains(['message'=>'Ville crée avec succès!']);

    }
}
