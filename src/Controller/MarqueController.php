<?php

namespace App\Controller;

use App\Repository\MarqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MarqueController extends AbstractController
{
    #[Route('/listeMarques', name: 'liste_Marques', methods: "GET")]
    public function listeMarques(MarqueRepository $doctrine, SerializerInterface $serializerInterface): JsonResponse
    {
        $liste = $doctrine->findAll();
        $listeJson = $serializerInterface->serialize($liste, 'json');

        return new JsonResponse($listeJson, 200, [], true);
    }
}