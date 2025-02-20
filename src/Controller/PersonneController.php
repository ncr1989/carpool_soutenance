<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/api')]
final class PersonneController extends AbstractController
{
    #[Route(name: 'app_personne_index', methods: ['GET'])]
    public function index(PersonneRepository $personneRepository): Response
    {
        return $this->render('personne/index.html.twig', [
            'personnes' => $personneRepository->findAll(),
        ]);
    }

    #[Route('/inscription', name: 'new_inscription', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $mdp = $data['mdp'];
        $confirmMdp = $data['confirmMdp'];

        if (empty($email) || empty($mdp) || empty($confirmMdp)) {
            return new JsonResponse(['error' => 'Remplir tous les champs!'], JsonResponse::HTTP_BAD_REQUEST);
        }
        if (!hash_equals($mdp, $confirmMdp)) {
            return new JsonResponse(['error' => 'Mots de passes '], JsonResponse::HTTP_BAD_REQUEST);
        }

        $existingUser = $entityManager->getRepository(Personne::class)->findOneBy(['email' => $email]);
        if ($existingUser) {
            return new JsonResponse(['error' => 'Cet email est déjà utilisé.'], JsonResponse::HTTP_CONFLICT);
        }

        $personne = new Personne();
        $hashedMdp = password_hash($mdp, PASSWORD_DEFAULT);
        $personne->setEmail($email);
        $personne->setMdp($hashedMdp);

        $entityManager->persist($personne);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur inscrit avec succès!'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/listeReservations/{id}', name: 'app_personne_listeReservations', methods: ['GET'])]
    public function listeReservations(Personne $personne): JsonResponse
    {
        $trajetsReserves =  $personne->getTrajetsReserves();

        $mesTrajetsReserves = [];
        foreach ($trajetsReserves as $trajet) {
            $mesTrajetsReserves = [
                'conducteur' => [$trajet->getPersonne()->getNom(), $trajet->getPersonne()->getPrenom()],
                'villeDepart' => $trajet->getVilleDepart()->getLabel(),
                'villeArrivee' => $trajet->getVilleArrivee()->getLabel(),
                'Nombre Places' => $trajet->getNbrPlaces(),
                'nom' => $trajet->getDateTrajet()
            ];
        }
        return new JsonResponse($mesTrajetsReserves, JsonResponse::HTTP_OK);
    }

    #[Route('/editProfile/{id}', name: 'app_personne_editCompte', methods: ['PUT'], requirements:["prenom"=>"[a-z]{3,30}","nom"=>"[a-z]{3,30}","email"=>"[a-z@.]{3,30}"])]
    public function edit(Request $request, Personne $personne, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
    if (!$data) {
        return new JsonResponse(['error' => 'Invalid JSON'], JsonResponse::HTTP_BAD_REQUEST);
    }
    if (!empty($data['nom'])) {
        $personne->setNom($data['nom']);
    }
    if (!empty($data['prenom'])) {
        $personne->setPrenom($data['prenom']);
    }
    if (!empty($data['email'])) {
        $personne->setEmail($data['email']);
    }
    if (!empty($data['tel'])) {
        $personne->setTel($data['tel']);
    }
    
    $entityManager->persist($personne);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Profile mise à jour!'], JsonResponse::HTTP_OK);
    }
    
    #[Route('/supprimeCompte/{id}', name: 'app_personne_supprimeCompte', methods: ['DELETE'])]
    public function delete(Request $request, Personne $personne, EntityManagerInterface $entityManager): JsonResponse
    {
        //$trajet->getPersonne()->removeTrajetsPropose($trajet);
        $entityManager->remove($personne);
        $entityManager->flush();

        return new JsonResponse(['Succés' => 'Compte supprime.'], JsonResponse::HTTP_OK);

        
    }
}
