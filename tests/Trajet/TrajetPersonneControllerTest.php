<?php



namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Personne;
use App\Tests\BaseTestCase;
use Doctrine\DBAL\Types\Types;
use App\Entity\Trajet;
use function PHPUnit\Framework\assertEmpty;


class TrajetPersonneControllerTest extends BaseTestCase{

    public function testListeTrajets():void {
        $response =  $this->client->request('GET', '/api/trajet/listeTrajets', [
              'headers' => [
                  'x-auth-token' => $this->token,
              ]
          ]);
  
          $data = $response->toArray();
          $this->assertNotEmpty($data);
          $this->assertEquals(count($data),3);
          $this->assertResponseStatusCodeSame(200);
          $this->assertArrayHasKey('nbrPlaces',$data[0]);
          $this->assertArrayHasKey('villeArrivee',$data[0]);
          $this->assertArrayHasKey('villeDepart',$data[0]);
          $this->assertArrayHasKey('dateTrajet',$data[0]);
          $this->assertArrayHasKey('conducteur',$data[0]);
      }

      public function testNewTrajet():void{
        $newTrajet = [
            "villeDepart" =>"baye",
            "villeArrivee" =>"coray",
            "personneId" =>1,
            "nbrPlaces"=>4,
            "dateTrajet"=>"2025-02-23T15:30:00"
        ];
            $response =  $this->client->request('POST', '/api/trajet/new', [
                'json' => $newTrajet,
                'headers' => [
                    'x-auth-token' => $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            $this->assertResponseStatusCodeSame(201);
            $this->assertJsonContains(['message'=>'Trajet inscrit avec succès!']);
      }

      public function testNewTrajetChampVide():void{
        $newTrajet = [
            "villeDepart" =>"baye",
            "villeArrivee" =>"coray",
            "personneId" =>1,
            "nbrPlaces"=>"",
            "dateTrajet"=>"2025-02-23T15:30:00"
        ];
            $response =  $this->client->request('POST', '/api/trajet/new', [
                'json' => $newTrajet,
                'headers' => [
                    'x-auth-token' => $this->token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            $this->assertResponseStatusCodeSame(400);
            $this->assertJsonContains(['error'=>'Remplir tous les champs!']);
      }

      public function testRechercheTrajetSuccess():void{
        $response = $this->client->request('GET', 'api/trajet/bodilis/baye/2025-02-19',[
            'headers' => ['x-auth-token'=>$this->token]
        ]);

        $data = $response->toArray();
        var_dump($data);
        $this->assertArrayHasKey('nbrPlaces',$data[0]);
        $this->assertArrayHasKey('villeArrivee',$data[0]);
        $this->assertArrayHasKey('dateTrajet',$data[0]);
        $this->assertArrayHasKey('conducteur',$data[0]);
        $this->assertArrayHasKey('villeDepart',$data[0]);
        $this->assertResponseStatusCodeSame(200);
        //$this->assertEquals(count($data),3);

      }

      public function testRechercheTrajetVilleNotFound():void{
        $response = $this->client->request('GET', 'api/trajet/paris/baye/2025-02-19',[
            'headers' => ['x-auth-token'=>$this->token]
        ]);
        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains(["error"=>"Ville not found"]);
      }

      public function testListePassgers():void{
        $response =$this->client->request('GET','api/trajet/listePassagers/2',[
            'headers'=>[
                'x-auth-token'=>$this->token
            ]
            ]);
            $data = $response->toArray();
            $this->assertArrayHasKey('prenom',$data[0]);
            $this->assertArrayHasKey('nom',$data[0]);
            $this->assertArrayHasKey('email',$data[0]);
            $this->assertArrayHasKey('telephone',$data[0]);
            $this->assertResponseStatusCodeSame(200); 
      }

      public function testDeleteTrajet():void{
        $response =$this->client->request('DELETE','api/trajet/2',[
            'headers'=>[
                'x-auth-token'=>$this->token
            ]
            ]);
            $data = $response->toArray();
            $this->assertResponseStatusCodeSame(200);
            $this->assertJsonContains(['Succés'=>'Trajet supprime.']);
            $deletedTrajet = $this->entityManager->getRepository(Trajet::class)->findOneBy(['id'=>2]);
            $this->assertEmpty($deletedTrajet);
      }

      public function testDetailsTrajet():void{
        $response =$this->client->request('GET','api/trajet/2',[
            'headers'=>[
                'x-auth-token'=>$this->token
            ]
            ]);
            $data = $response->toArray();

            $this->assertResponseStatusCodeSame(200);
            $this->assertArrayHasKey('nbrPlaces',$data);
            $this->assertArrayHasKey('villeArrivee',$data);
            $this->assertArrayHasKey('villeDepart',$data);
            $this->assertArrayHasKey('dateTrajet',$data);
            $this->assertArrayHasKey('conducteur',$data);
            $this->assertArrayHasKey('nom',$data['conducteur']);
            $this->assertArrayHasKey('prenom',$data['conducteur']);
            
           
      }


    }