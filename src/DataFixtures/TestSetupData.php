<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Personne;
use App\Entity\Trajet;
use App\Entity\Ville;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class TestSetupData extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager):void
    {


        //ville objects 


        $personnes = [['nom' => 'testMe','prenom'=>'Lemon','email'=>'test@gmail.com','pseudo'=>'test', 'mdp' => 'adminpass', 'api_token' => '1009 72']];
        foreach ($personnes as $key => $personne) {
            $objPersonne = new Personne;
            $objPersonne->setNom($personne['nom']);
            $objPersonne->setPrenom($personne['prenom']);
            $objPersonne->setPseudo($personne['pseudo']);
            $objPersonne->setEmail($personne['email']);
            $objPersonne->setMdp($this->hasher->hashPassword($objPersonne, $personne['mdp']));
            $objPersonne->setApiToken($personne['api_token']);
            $manager->persist($objPersonne);
        }
        $baye = new Ville();
        $bodilis= new Ville();
        $brennilis = new Ville();

        $baye->setId(68);
        $baye->setInseeCode(29005);
        $baye->setzipCode(29300);
        $baye->setCityCode("baye");
        $baye->setlabel("baye");
        $baye->setLatitude(47.85584953);
        $baye->setLongitude(-3.613231761);
        
        $baye->setDepartmentName("finistre");
        $baye->setDepartmentNumber(29);
        $baye->setRegionName("bretagne");
        $baye->setRegionGeojsonName("Bretagne");

        //bodilis 
        $bodilis->setId(69);
        $bodilis->setInseeCode(29010);
        $bodilis->setzipCode(29400);
        $bodilis->setCityCode("bodilis");
        $bodilis->setlabel("bodilis");
        $bodilis->setLatitude(48.515319658);
        $bodilis->setLongitude(-4.120072961);
        
        $bodilis->setDepartmentName("finistre");
        $bodilis->setDepartmentNumber(29);
        $bodilis->setRegionName("bretagne");
        $bodilis->setRegionGeojsonName("Bretagne");

        //brennillis 
        $brennilis->setId(70);
        $brennilis->setInseeCode(29010);
        $brennilis->setzipCode(29690);
        $brennilis->setCityCode("brennilis");
        $brennilis->setlabel("brennilis");
        $brennilis->setLatitude(48.359908338);
        $brennilis->setLongitude(-3.85090142);
        
        $brennilis->setDepartmentName("finistre");
        $brennilis->setDepartmentNumber(29);
        $brennilis->setRegionName("bretagne");
        $brennilis->setRegionGeojsonName("Bretagne");
        

        $manager->persist($baye);
        $manager->persist($bodilis);
        $manager->persist($brennilis);


        
        
        

        
       
        $dateString = "2025-02-19 15:30";
        $dateTime = new \DateTime($dateString);

        //trajet1
        $trajet1 = new Trajet();
        $trajet1->setId(2);
        $trajet1->setVilleArrivee($baye);
        $trajet1->setVilleDepart($bodilis);
        $trajet1->setPersonne($objPersonne);
        $trajet1->setDateTrajet($dateTime);
        $trajet1->setNbrPlaces(3);

        $manager->persist($trajet1);

        //trajet2
        $trajet2 = new Trajet();
        $dateString2 = "2025-02-23 15:30";
        $dateTime2 = new \DateTime($dateString2);
        $trajet2->setId(3);
        $trajet2->setVilleArrivee($brennilis);
        $trajet2->setVilleDepart($brennilis);
        $trajet2->setPersonne($objPersonne);
        $trajet2->setDateTrajet($dateTime2);
        $trajet2->setNbrPlaces(3);
           
        $manager->persist($trajet2);


        //trajet3
        $trajet3 = new Trajet();
        $trajet3->setId(4);
        $trajet3->setVilleArrivee($brennilis);
        $trajet3->setVilleDepart($brennilis);
        $trajet3->setPersonne($objPersonne);
        $trajet3->setDateTrajet($dateTime2);
        $trajet3->setNbrPlaces(2);
           
        $manager->persist($trajet3);


        //reservations 
        $trajet1->addPassager($objPersonne);
        $trajet2->addPassager($objPersonne);
        $trajet3->addPassager($objPersonne);
    
        
        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['group3'];
    }
}
