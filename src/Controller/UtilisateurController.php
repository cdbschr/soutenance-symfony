<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncode)
    {
        $this->passwordEncoder = $passwordEncode;
    }

    #[Route('/creationCompte', name: 'creation_compte', methods: 'POST')]
    public function creationCompte(Request $request, UserPasswordHasherInterface $passEncoder, EntityManagerInterface $em): JsonResponse
    {
        if ($request->isMethod('post')) {
            $utilisateur = new Utilisateur;
            $utilisateur->setNom($request->query->get('username'));
            $motDePasse_hashe = $passEncoder->hashPassword($utilisateur, $request->query->get('password'));
            $utilisateur->setPassword($motDePasse_hashe);

            $verification_utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['nom' => $request->query->get('username')]);

            if ($verification_utilisateur) {
                $resultat = ["NOK" => "Ce nom d'utilisateur est déjà utilisé"];
                return new JsonResponse($resultat);

            } else {
                $em->persist($utilisateur);
                $em->flush();

                if (null === $utilisateur->getId()) {
                    $resultat = ["NOK" => "Erreur lors de la création du compte, merci de réessayer plus tard"];
                    return new JsonResponse($resultat);

                } else {
                    $resultat = ['id_user' => $utilisateur->getId()];
                    return new JsonResponse($resultat);
                }
            }
        }
    }

    #[Route('/connexion', name: 'connexion')]
    public function authentification(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $nom = $request->query->get('username');
        $password = $request->query->get('password');
        $utilisateur = new Utilisateur;
        $utilisateur->setNom($nom);
        $utilisateur->setPassword($this->passwordEncoder->hashPassword($utilisateur, $password));

        $em = $doctrine->getManager();
        $userRepository = $em->getRepository(Utilisateur::class);

        $nom_utilisateur = $userRepository->findOneBy(['nom' => $nom]);

        if ($nom_utilisateur) {
            if (!$this->passwordEncoder->isPasswordValid($nom_utilisateur, $password)) {
                $resultat = ["NOK" => "mot de passe invalide"];

            } else {
                $randomBytes = random_bytes(16);
                $token_api = bin2hex($randomBytes);
                $nom_utilisateur->setTokenApi($token_api);
                $em->persist($nom_utilisateur);
                $em->flush();
                $resultat = ['id_user' => $nom_utilisateur->getId(), 'token' => $nom_utilisateur->getTokenApi()];
            }
        } else {
            $resultat = ["NOK" => "nom d'utilisateur invalide"];
        }
        $reponse = new JsonResponse($resultat);
        return $reponse;
    }

    #[Route('/deconnexion', name: 'deconnexion')]
    public function deconnexion(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $token_api = $request->headers->get("token_api");
            $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['token_api' => $token_api]);
            $utilisateur->setTokenApi(null);

            $em->persist($utilisateur);
            $em->flush();

            $resultat = ["Vous êtes bien déconnecté"];
            return new JsonResponse($resultat);

        } catch (\Error $e) {
            $resultat = ["Vous êtes déjà déconnecté"];
            return new JsonResponse($resultat);
        }
    }

    #[Route('/listeUtilisateur', name: 'liste_utilisateur')]
    public function listeUtilisateur(ManagerRegistry $doctrine): JsonResponse
    {
        $em = $doctrine->getManager();
        $userRepository = $em->getRepository(Utilisateur::class);
        $utilisateurs = $userRepository->findAll();
        $resultat = [];
        foreach ($utilisateurs as $utilisateur) {
            $resultat[] = [
                'id' => $utilisateur->getId(),
                'nom' => $utilisateur->getNom(),
                'token_api' => $utilisateur->getTokenApi(),
                'roles' => $utilisateur->getRoles()
            ];
        }
        $reponse = new JsonResponse($resultat);
        return $reponse;
    }
}