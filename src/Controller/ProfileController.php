<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

class ProfileController extends AbstractController
{
    private $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    #[Route('/profile/{id}', name: 'profile_show', methods: ['GET'])]
    public function show(int $id, SerializerInterface $serializer): JsonResponse
    {
        $profile = new Profile(); // Instaciamos objeto
        $response = array(); // Infomacion que le devolveremos al cliente

        try {
            $profile = $this->profileRepository->findOneBy(['id' => $id]);
        } catch (Throwable $th) {
            $response['message'] = "Error";
            $response['error'] = "An exception occurred while searching the profile";
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if($profile == null){
            $response['message'] = "Could not find profile";
            $response['error'] = "The profile your trying to access does not exist";
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }
        // Pasamos el perfil al cliente en formato JSON
        $data = [
            'id' => $profile->getId(),
            'username' => $profile->getUsername(),
            'firstName' => $profile->getFirstName(),
            'lastName' => $profile->getLastName(),
            'profilePic' => $profile->getProfilePic(),
            'bannerPic' => $profile->getBannerPic(),
            'biography' => $profile->getBiography(),
            'website' => $profile->getWebsite()
        ];
        $response['profile'] = $data;
        // $response['profile'] = $serializer->serialize($profile, );
        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/profile/{id}', name:'profile_edit', methods:'PUT')]
    public function update(Request $request, int $id): JsonResponse{

        $profile = $this->profileRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true); // Convertimos los datos enviados por el cliente en un array associativo
        $response = array(); // Infomacion que le devolveremos al cliente

        // Actualizamos los datos del perfil
        try {
            $profile->setUsername($data['username']);
            $profile->setFirstName($data['firstName']);
            $profile->setLastName($data['lastName']);
            $profile->setProfilePic($data['profilePic']);
            $profile->setBannerPic($data['bannerPic']);
            $profile->setBiography($data['biography']);
            $profile->setWebsite($data['website']);
            $this->profileRepository->save($profile, true);
        } catch (Throwable $th) {
            $response['message'] = "Error";
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
            'biography' => $profile->getBiography(),
            'website' => $profile->getWebsite()
        ];
        $response['message'] = "Profile updated successfully!";
        $response['profile'] = $data;
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
