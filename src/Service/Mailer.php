<?php
namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Email;
use App\Entity\Personne;
use App\Entity\Trajet;
use App\Entity\Ville;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\EntityManagerProvider;

class Mailer{

    public function sendEmail(MailerInterface $mailer, Trajet $trajet,EntityManagerInterface $entityManager): Response
    {
        $villeDepart = $entityManager->getRepository(Ville::class)->findOneBy(['id'=>$trajet->getVilleDepart()]);
        $villeArrive = $entityManager->getRepository(Ville::class)->findOneBy(['id'=>$trajet->getVilleDepart()]);
        $passagersArray = $trajet->getPassagers();
        foreach($passagersArray as $passager){
        $email = (new Email())
            ->from('test@example.com')
            ->to($passager->getEmail())
            ->subject('Covoiturage annulé')
            ->text('')
            ->html('<p>Le trajet de '+$villeDepart->getLabel()+' à '+$villeArrive->getLabel()+'prevu le '+$trajet->getDateTrajet()+' était annulé par le conducteur. </p>')
            ->html('<p>L\'equipe carpool </p>');

        try {
            $mailer->send($email);
            return new Response('Email sent successfully!');
        } catch (\Exception $e) {
            return new Response('Failed to send email: ' . $e->getMessage());
        }
    }
    }



}
