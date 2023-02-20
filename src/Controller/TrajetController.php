<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\Trajet;
use App\Entity\Ville;
use App\Repository\TrajetRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TrajetController extends AbstractController
{
    #[Route('/insertionTrajet', name: 'insertion_trajet', methods: "POST")]
    public function insertionTrajet(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if ($request->isMethod("post")) {
            $cherche = ['-', "'", 'é', 'è', 'ê', 'à', 'É', 'û', 'ô', 'ö', 'ò', 'ù', 'ÿ', 'Ö', 'á', 'í', 'ú', 'î', 'Å', 'ë', 'À', 'Á', 'Â', 'Ã', 'Å', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'È', 'Ê', 'Ì', 'Í', 'Î', 'Ï'];
            $remplace = [' ', " ", 'e', 'e', 'e', 'a', 'E', 'u', 'o', 'o', 'o', 'u', 'y', 'O', 'a', 'i', 'u', 'i', 'a', 'e', 'a', 'a', 'a', 'a', 'a', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'e', 'e', 'i', 'i', 'i', 'i'];

            $ville_depart = strtolower(str_replace($cherche, $remplace, $request->query->get('ville_depart')));
            $ville_arrivee = strtolower(str_replace($remplace, $remplace, $request->query->get('ville_arrivee')));

            $datetime = new DateTime($request->query->get('date'));
            $heure_depart = new DateTime($request->query->get('heureDepart'));
            $heure_arrivee = new DateTime($request->query->get('heureArrivee'));

            $id = $request->query->get('id_pers');

            $depart = $em->getRepository(Ville::class)->findOneBy(['nom' => $ville_depart]);
            $arrivee = $em->getRepository(Ville::class)->findOneBy(['nom' => $ville_arrivee]);
            $personne = $em->getRepository(Personne::class)->findOneBy(['id' => $id]);

            $trajet = new Trajet;
            $trajet->setKms($request->query->get('kms'));
            $trajet->setVilledepart($depart);
            $trajet->setVillearrivee($arrivee);
            $trajet->setConducteur($personne);
            $trajet->setVoiture($personne->getVoiture()[0]);

            if ($request->query->get('places') > $personne->getVoiture()[0]->getPlaces()) {
                return new JsonResponse(['error' => 'Plus de places disponibles'], 404);

            } else {
                $trajet->setPlacedispos($request->query->get('places'));
            }

            $trajet->setHeuredepart($heure_depart);
            $trajet->setHeurearrivee($heure_arrivee);
            $trajet->setDate($datetime);

            $em->persist($trajet);
            $em->flush();
            $resultat = ["OK" => "Trajet ajouté"];
        }
        return new JsonResponse($resultat);
    }
    #[Route('/listeTrajet', name: 'liste_trajet', methods: "GET")]
    public function listeTrajet(TrajetRepository $doctrine, SerializerInterface $serializerInterface): JsonResponse
    {
        $liste = $doctrine->findAll();
        $listeJson = $serializerInterface->serialize($liste, 'json');

        return new JsonResponse($listeJson, 200, [], true);
    }

    #[Route('/rechercheTrajet', name: 'recherche_trajet', methods: "GET")]
    public function rechercheTrajet(Request $request, EntityManagerInterface $em, TrajetRepository $doctrine): JsonResponse
    {
        $cherche = ['-', "'", 'é', 'è', 'ê', 'à', 'É', 'û', 'ô', 'ö', 'ò', 'ù', 'ÿ', 'Ö', 'á', 'í', 'ú', 'î', 'Å', 'ë', 'À', 'Á', 'Â', 'Ã', 'Å', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'È', 'Ê', 'Ì', 'Í', 'Î', 'Ï'];
        $remplace = [' ', " ", 'e', 'e', 'e', 'a', 'E', 'u', 'o', 'o', 'o', 'u', 'y', 'O', 'a', 'i', 'u', 'i', 'a', 'e', 'a', 'a', 'a', 'a', 'a', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'e', 'e', 'i', 'i', 'i', 'i'];

        $ville_depart = strtolower(str_replace($cherche, $remplace, $request->query->get('ville_depart')));
        $ville_arrivee = strtolower(str_replace($remplace, $remplace, $request->query->get('ville_arrivee')));

        $depart = $em->getRepository(Ville::class)->findOneBy(['nom' => $ville_depart]);
        $arrivee = $em->getRepository(Ville::class)->findOneBy(['nom' => $ville_arrivee]);
        $datetime = new DateTime($request->query->get('date'));

        $trajets = $doctrine->findBy((['ville_depart' => $depart, 'ville_arrivee' => $arrivee]));

        if (!$trajets) {
            return new JsonResponse(['error' => 'Trajets inconnu...'], 404);

        } else {

            $resultat = array();
            foreach ($trajets as $trajet) {
                $trajetArray = array(
                    [
                        'id' => $trajet->getId(),
                        'Kms' => $trajet->getKms(),
                        'date' => $trajet->getDate()->format('d-m-Y'),
                        'heureDepart' => $trajet->getHeuredepart()->format('H:i'),
                        'heureArrivee' => $trajet->getHeurearrivee()->format('H:i'),
                        'places' => $trajet->getPlacedispos(),
                        'conducteurNom' => $trajet->getConducteur()->getNom(),
                        'conducteurPrenom' => $trajet->getConducteur()->getPrenom()
                    ]
                );
                $resultat[] = $trajetArray;
            }
            $reponse_json = new JsonResponse($resultat);

            return $reponse_json;
        }
    }


    #[Route('/supprimeTrajet/{id}', name: 'supprime_trajet', methods: "DELETE")]
    public function supprimeTrajet(int $id, TrajetRepository $doctrine, EntityManagerInterface $em): JsonResponse
    {
        $trajet = $doctrine->find($id);

        if (!$trajet) {
            return new JsonResponse(['error' => 'Trajet inconnu...'], 404);
        } else {
            foreach ($trajet->getPassagers() as $personne) {
                $personne->removeTrajetsReserf($trajet);
            }
            $trajet->setConducteur(null);
            $trajet->setVoiture(null);
            $trajet->setVillearrivee(null);
            $trajet->setVilledepart(null);
            $em->remove($trajet);
            $em->flush();

            return new JsonResponse(['message' => 'Trajet eliminé'], 200);
        }
    }
}