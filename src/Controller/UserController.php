<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class UserController extends AbstractController
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/user/{id}', name: 'user_delete')]
    public function delete(int $id): JsonResponse
    {
        $response = array(); // Infomacion que le devolveremos al cliente

        try {
            $user = $this->userRepository->findOneBy(['id' => $id]);
            $this->userRepository->remove($user, true);
        } catch (Throwable $th) {
            $response['message'] = "Error deleting user in database";
            $response['error'] = $th->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $response['message'] = "User deleted successfullt";
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
