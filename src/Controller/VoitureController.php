<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Entity\Personne;
use App\Entity\Voiture;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VoitureController extends AbstractController
{
    #[Route('/insertionVoiture', name: 'insertion_Voiture', methods: "POST")]
    public function insertionVoiture(Request $request, EntityManagerInterface $em, VoitureRepository $doctrine): JsonResponse
    {

        $immatriculation = $request->query->get('immatriculation');
        $immat = $doctrine->findOneBy(['immatriculation' => $immatriculation]);

        if ($immat) {
            $resultat = ["NOK" => "La voiture est déjà enregistrée"];
            return new JsonResponse($resultat);

        } else {
            $voiture = new Voiture;
            $utilisateur = $em->getRepository(Personne::class)->findOneBy(["id" => $request->query->get("id_pers")]);
            $voiture->setConducteur($utilisateur);
            $voiture->setImmatriculation($immatriculation);
            $voiture->setCouleur($request->query->get('color'));
            $voiture->setPlaces($request->query->get('places'));
            $voiture->setMarque($em->getRepository(Marque::class)->findOneBy(['nom' => strtoupper($request->query->get('marque'))]));

            $utilisateur->addVoiture($voiture);
            $em->persist($utilisateur);
            $em->persist($voiture);

            $em->flush();
            $resultat = ["OK" => "La voiture a bien été ajoutée"];

            return new JsonResponse($resultat);
        }
    }

    #[Route('/listeVoiture', name: 'liste_Voiture', methods: "GET")]
    public function listeVoiture(VoitureRepository $doctrine): JsonResponse
    {
        $liste = $doctrine->findAll();
        $response = new JsonResponse();

        foreach ($liste as $voiture) {
            $response->setData(
                [
                    'id' => $voiture->getId(),
                    'matricule' => $voiture->getImmatriculation(),
                    'color' => $voiture->getCouleur(),
                    'places' => $voiture->getPlaces(),
                    'marque' => $voiture->getMarque()->getNom(),
                    'Nom Conducteur' => $voiture->getConducteur()->getNom(),
                    'Prenom Conducteur' => $voiture->getConducteur()->getPrenom(),
                ]
            );
        }
        return $response;
    }

    #[Route('/supprimerVoiture/{id}', name: 'supprimer_Voiture', methods: "DELETE")]
    public function supprimerVoiture(int $id, VoitureRepository $doctrine, EntityManagerInterface $em): JsonResponse
    {
        $voiture = $doctrine->find($id);

        if (!$voiture) {
            return new JsonResponse(['error' => "La voiture n'est pas reconnu dans nos bases de données"], 404);
        } else {

            $voiture->setConducteur(null);
            $em->remove($voiture);
            $em->flush();
            return new JsonResponse(['message' => 'La voiture a bien été enlevée'], 200);
        }

    }
}