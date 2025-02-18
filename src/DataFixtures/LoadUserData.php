<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Personne;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class LoadUserData extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public function load(ObjectManager $manager):void
    {
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
        }             // mise à jour de la base avec tous les persists         
        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['group3'];
    }
}
