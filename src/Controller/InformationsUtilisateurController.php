<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Entity\Personne;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class InformationsUtilisateurController extends AbstractController
{
    #[Route('/insertionPersonne', name: 'insertion_personne', methods: "POST")]
    public function insertionPersonne(Request $request, PersonneRepository $personneRepository, EntityManagerInterface $em): JsonResponse
    {
        $email = $request->query->get('email');
        $personne = $personneRepository->findOneBy(['email' => $email]);

        if ($personne) {
            return new JsonResponse(['error' => 'Utilisateur déjà enregistré dans notre base de données'], 404);

        } else {
            $personneEntity = new Personne;
            $personneEntity->setPrenom($request->query->get('prenom'));
            $personneEntity->setNom($request->query->get('nom'));
            $personneEntity->setEmail($email);

            if (!empty($request->query->get('telephone')) && $request->query->get('telephone') != null) {
                $personneEntity->setTel($request->query->get('telephone'));
            }

            if (!empty($request->query->get('possedeVoiture') && ($request->query->get('possedeVoiture') != null))) {
                if ($request->query->get('possedeVoiture')) {
                    $voiture = new Voiture;
                    $voiture->setConducteur($personneEntity);
                    $voiture->setImmatriculation($request->query->get('plaque'));
                    $voiture->setCouleur($request->query->get('couleur'));
                    $voiture->setPlaces($request->query->get('places'));
                    $voiture->setMarque($em->getRepository(Marque::class)->findOneBy(['nom' => strtoupper($request->query->get('marque'))]));

                    $personneEntity->addVoiture($voiture);
                    $em->persist($voiture);
                }
            }
            $utilisateurRepository = $em->getRepository(Utilisateur::class);
            $utilisateur = $utilisateurRepository->findOneBy(['token_api' => $request->headers->get('token_api')]);

            $personneEntity->setIdUser($utilisateur);
            $em->persist($personneEntity);
            $em->flush();

            return new JsonResponse(['message' => 'Personne ajoutée...'], 200);
        }
    }

    #[Route('/listePersonnes', name: 'listePersonnes', methods: "GET")]
    public function listePersonne(PersonneRepository $personneRepository, SerializerInterface $serializerInterface): JsonResponse
    {
        $liste = $personneRepository->findAll();
        $listeJson = $serializerInterface->serialize($liste, 'json', ['groups' => ['info']]);

        return new JsonResponse($listeJson, 200, [], true);
    }

    #[Route('/liste/personne/{id}', name: 'selection_personne', methods: "GET")]
    public function listeUnePersonne(int $id, PersonneRepository $personneRepository, SerializerInterface $serializerInterface): JsonResponse
    {
        $personne = $personneRepository->find($id);

        if (!$personne) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé, veuillez réessayer'], 404);

        } else {
            $personneJson = $serializerInterface->serialize($personne, 'json', ['groups' => ['info']]);
            $affichage_personne = new JsonResponse($personneJson, 200, [], true);
            $voitures = $personne->getVoiture();

            $array = array();
            foreach ($voitures as $voiture) {
                $informations_voiture = array(
                    [
                        'cars' => $voiture,
                        'id' => $voiture->getId(),
                        'immatriculation' => $voiture->getImmatriculation(),
                        'color' => $voiture->getCouleur(),
                        'places' => $voiture->getPlaces(),
                        'marque' => $voiture->getMarque()->getNom(),
                        'modele' => $voiture->getMarque()->getNom()
                    ]
                );
                $array[] = $informations_voiture;
            }

            $contenu = json_decode($affichage_personne->getContent(), true);
            $fusion = array_merge($contenu, $array);

            $reponse = new JsonResponse($fusion);
            return $reponse;
        }
    }

    #[Route("/miseajourPersonne/{id}", name: "miseajour_personne", methods: "PUT")]
    public function miseajourPersonne(int $id, Request $request, PersonneRepository $personneRepository, EntityManagerInterface $em, UserPasswordHasherInterface $passEncoder): JsonResponse
    {
        $personne = $personneRepository->find($id);
        if ($personne) {
            $utilisateur = $em->getRepository(Utilisateur::class)->find($personne->getIdUser());

            if (!empty($request->query->get('nom')) && $request->query->get('nom') != null) {
                $personne->setNom($request->query->get('nom'));
            }

            if (!empty($request->query->get('prenom')) && $request->query->get('prenom') != null) {
                $personne->setPrenom($request->query->get('prenom'));
            }

            if (!empty($request->query->get('email')) && $request->query->get('email') != null) {
                $personne->setEmail($request->query->get('email'));
            }

            if (!empty($request->query->get('tel')) && $request->query->get('tel') != null) {
                $personne->setTel($request->query->get('tel'));
            }

            if (!empty($request->query->get('login')) && $request->query->get('login') != null) {
                $utilisateur->setNom($request->query->get('login'));
            }

            if (!empty($request->query->get('password')) && $request->query->get('password') != null) {
                $hashedPassword = $passEncoder->hashPassword($utilisateur, $request->query->get('password'));
                $utilisateur->setPassword($hashedPassword);
            }

            $personne->setIdUser($utilisateur);
            $em->persist($personne);
            $em->flush();
            $em->flush();
            $resultat = ["Mise à jour de vos données réalisées avec succès"];

        } else {
            $resultat = ["NOK" => "Utilisateur non existant"];
        }
        return new JsonResponse($resultat);
    }

    #[Route('/supprimerPersonne/{id}', name: 'supprimer_personne', methods: "DELETE")]
    public function supprimerPersonne(int $id, PersonneRepository $doctrine, EntityManagerInterface $em): JsonResponse
    {
        $personne = $doctrine->find($id);

        if (!$personne) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé, veuillez réessayer'], 404);
        } else {
            $em->remove($personne);
            $em->flush();

            return new JsonResponse(['message' => "L'utilisateur a bien été enlevé de notre base de données"], 200);
        }
    }
}