<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class LoginController extends AbstractController
{
   private $userRepository;

    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    #[Route('/login', name: 'login_user', methods:['POST'])]
    public function verify(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = new User(); // Instanciamos un nuevo objeto 
        $profile = new Profile();
        $data = json_decode($request->getContent(), true); // Convertimos los datos enviados por el cliente en un array associativo
        $response = array(); // Contendra la informacion que le devolveremos al cliente

        // Capturamos las excepciones si ocurre algun error a la hora de buscar el usuario en la BBDD
        try {
            $user = $this->userRepository->findOneBy(['email' => $data['email']]);
            // $profile = get_object_vars($user->getProfile());
        } catch (Throwable $th) {
            $response['message'] = "Error while trying to log in";
            $response['error'] = $th->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Comprobamos si la contraseña ingresada es igual a la cotraseña ecriptada a la BBDD
        if(!$passwordHasher->isPasswordValid($user, $data['plainPassword'])){
            $response['message'] = "Password Incorrect";
            return new JsonResponse($response, Response::HTTP_UNAUTHORIZED);
        }
        $response['message'] = "User logged successfully!";
        // $response['user'] = array($user->getUsername(), $user->getFirstName(), $user->getLastName(), $user->getProfile()->getProfilePic(), $user->getProfile()->getBannerPic(), $user->getProfile()->getBiography());
        return new JsonResponse($response, Response::HTTP_ACCEPTED);
    }
}
