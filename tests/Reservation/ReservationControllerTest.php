<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Personne;
use App\Tests\BaseTestCase;
use Doctrine\DBAL\Types\Types;
use App\Entity\Trajet;


class ReservationControllerTest extends BaseTestCase {

    public function testDetailsTrajet():void{
       $response =  $this->client->request('GET', '/api/reservation/2', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $data = $response->toArray();
        
        $date = new \DateTime("2025-02-23 15:30"); 
        $expectedDate = new \DateTime("2025-02-23");
        $this->assertEquals("brenillis",$data['villeArrivee']);
        $this->assertEquals("brenillis",$data['villeDepart']);
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
    public function testReserverTrajet():void{
       
        $response =  $this->client->request('POST', '/api/reservation/1/2', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['message'=>"Reservation effectué!"]);

    }

    public function testPlusDePlace():void{
        $trajet = $this->entityManager->getRepository(Trajet::class)->findOneBy(["id"=>2]);
        $nbrPlaces = $trajet->getNbrPlaces();
        for($i=0;$i<$nbrPlaces;$i++){
            $response =  $this->client->request('POST', '/api/reservation/1/2', [
                'headers' => [
                    'x-auth-token' => $this->token,
                ]
            ]);
        }

        $response = $this->client->request('POST', '/api/reservation/1/2', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
    
        
        $this->assertResponseStatusCodeSame(400); // Assuming 400 is returned for "Plus de place"
        $this->assertJsonContains(['message' => "Plus de place"]);
       
    }

    public function testAnnuleReservation():void{
        $trajet = $this->entityManager->getRepository(Trajet::class)->findOneBy(["id"=>2]);
        $nbrPlaces = $trajet->getNbrPlaces();
       $response =  $this->client->request('DELETE', 'api/reservation/annuleReservation/1/2', [
            'headers' => [
                'x-auth-token' => $this->token,
            ]
        ]);
        $trajet = $this->entityManager->getRepository(Trajet::class)->findOneBy(["id"=>2]);
        $afterPlaces = $trajet->getNbrPlaces();
                $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['message'=>'Reservation annulé.']);
        $this->assertNotEquals($nbrPlaces,$afterPlaces);
           
}


}
