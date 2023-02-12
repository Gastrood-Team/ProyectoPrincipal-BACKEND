<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class ProfileController extends AbstractController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    #[Route('/profile/{id}', name: 'profile_show', methods: ['GET'])]
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

        // Pasamos el perfil al cliente en formato JSON
        $data = [
            'username' => $profile->getUsername(),
            'firstName' => $profile->getFirstName(),
            'lastName' => $profile->getLastName(),
            'profilePic' => $profile->getProfilePic(),
            'bannerPic' => $profile->getBannerPic(),
            'biography' => $profile->getBiography()
        ];
        $response['profile'] = $data;
        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/profile/{id}', name:'profile_edit', methods:'PUT')]
    public function update(Request $request, int $id): JsonResponse{

        $profile = $this->profileRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true); // Convertimos los datos enviados por el cliente en un array associativo
        $response = array(); // Infomacion que le devolveremos al cliente

        // Devolvemos un HTTP Response 404 si no encuentra el perfil
        if($profile == null){
            $response['message'] = "Error: Could not edit profile";
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        // Actualizamos los datos del perfil
        try {
            $profile->setUsername($data['username']);
            $profile->setFirstName($data['firstName']);
            $profile->setLastName($data['lastName']);
            $profile->setProfilePic($data['profilePic']);
            $profile->setBannerPic($data['bannerPic']);
            $profile->setBiography($data['biography']);
            $this->profileRepository->save($profile, true);
        } catch (Throwable $th) {
            $response['message'] = "Error updating profile in database";
            $response['error'] = $th->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Pasamos el nuevo perfil al cliente en formato JSON
        $data = [
            'username' => $profile->getUsername(),
            'firstName' => $profile->getFirstName(),
            'lastName' => $profile->getLastName(),
            'profilePic' => $profile->getProfilePic(),
            'bannerPic' => $profile->getBannerPic(),
            'biography' => $profile->getBiography()
        ];
        $response['message'] = "Profile updated successfully!";
        $response['profile'] = $data;
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
