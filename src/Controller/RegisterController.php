<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class RegisterController extends AbstractController
{
    private $userRepository;
    private $profileRepository;

    public function __construct(UserRepository $userRepository, ProfileRepository $profileRepository)
    {
        $this->userRepository = $userRepository;
        $this->profileRepository = $profileRepository;
    }

    #[Route('/register', name: 'register_user', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
    
        $user = new User(); // Instanciamos un nuevo usario 
        $profile = new Profile();  // Instiamos un nuevo perfil para el nuevo usuario
        $data = json_decode($request->getContent(), true); // Convertimos los datos enviados por el cliente en un array associativo
        $response = array(); // Contendra la informacion que le devolveremos al cliente

        // Capturamos las excepciones si ocurre algun error a la hora de registrar el usuario
        try {
            $profile->setUsername($data['profile']['firstName'] . $data['profile']['lastName']);
            $profile->setFirstName($data['profile']['firstName']);
            $profile->setLastName($data['profile']['lastName']);

            $user->setEmail($data['email']);
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
            $user->setRole('client');
            $user->setProfile($profile);
            
            $this->profileRepository->save($profile, true);
            $this->userRepository->save($user, true);
        } catch (Throwable $th) {
            $response['message'] = "Error while creating the user in the database";
            $response['error'] = $th->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response['message'] = "User registered successfully!";
        return new JsonResponse($response, Response::HTTP_CREATED);
    }
}
