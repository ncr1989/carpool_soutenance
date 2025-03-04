<?php

namespace App\Tests\Security\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Personne;
use App\Tests\BaseTestCase;
use Doctrine\DBAL\Types\Types;


class ReservationControllerTest extends BaseTestCase {

    public function testDetailsTrajet():void{
       $response =  $this->client->request('GET', '/api/reservation/2', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $data = $response->toArray();
        
        $date = new \DateTime("2025-02-19 15:30"); // Correct DateTime instantiation
        $expectedDate = new \DateTime("2025-02-19");
        $this->assertEquals("baye",$data['villeArrivee']);
        $this->assertEquals("bodilis",$data['villeDepart']);
        $this->assertEquals(3,$data['nbrPlaces']);
        $this->assertEquals($expectedDate->format('Y-m-d'), (new \DateTime($data['dateTrajet']['date']))->format('Y-m-d'));
        $this->assertResponseStatusCodeSame(200);
    }

    public function testListeReservations():void {
      $response =  $this->client->request('GET', '/api/reservation/listeReservations/1', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);

        $data = $response->toArray();
        $this->assertNotEmpty($data);
        $this->assertEquals(count($data),3);
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals("testMe",$data[0]["conducteur"]["nom"]);
        $this->assertEquals("Lemon",$data[0]["conducteur"]["prenom"]);
    }

}
