<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\Trajet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class InscriptionController extends AbstractController
{
    #[Route('/listeInscription', name: 'liste_inscription')]
    public function listeIncription(SerializerInterface $serializerInterface, EntityManagerInterface $em): JsonResponse
    {
        $connexion = $em->getConnection();

        $requete = "SELECT * FROM personne_trajet";

        $statement = $connexion->prepare($requete);
        $resultSet = $statement->executeQuery();
        $passagers = $resultSet->fetchAllAssociative();

        $listeJson = $serializerInterface->serialize($passagers, 'json');

        return new JsonResponse($listeJson, 200, [], true);
    }

    #[Route('/insertionInscription', name: 'insertion_inscription')]
    public function insertIncription(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $id_utilisateur = $request->query->get('id_pers');
        $id_trajet = $request->query->get('id_trajet');

        $utilisateur = $em->getRepository(Personne::class)->findOneBy(["id" => $id_utilisateur]);
        $trajet = $em->getRepository(Trajet::class)->findOneBy(["id" => $id_trajet]);

        if ($trajet->getPlacedispos() >= $trajet->getPassagers()->count()) {
            $utilisateur->addTrajetsReserf($trajet);
            $em->persist($utilisateur);
            $em->flush();

            $resultat = ["OK" => "Vous êtes bien enregistrés sur ce trajet"];

        } else {
            $resultat = ["NOK" => "Malheureusement, le trajet est complet"];
        }

        return new JsonResponse($resultat);
    }
}