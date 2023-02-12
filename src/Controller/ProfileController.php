<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Doctrine\Bundle\DoctrineBundle\Controller\ProfilerController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class UserController extends AbstractController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->$profileRepository = $profileRepository;
    }

    #[Route('/profile/{id}', name: 'profile_show', methods:['GET'])]
    public function show(int $id): JsonResponse
    {
        $profile = new Profile(); // Instaciamos objeto
        $response = array(); // Infomacion que le devolveremos al cliente
        try {
            $profile = $this->profileRepository->findOneBy(['id' => $id]);
        } catch (Throwable $th) {
            $response['message'] = "Error while querying to the database";
            $response['error'] = $th->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
    }
}
