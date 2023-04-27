<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $_userService;

    public function __construct(UserService $userService){
        $this->_userService = $userService;
    }

    #[Route('user', name: 'get-user', methods:'GET')]
    public function getLoggedUser(): JsonResponse
    {
        try {

            $user = $this->_userService->getLoggedUser();
            
            $response['status'] = 'success';
            $response['data'] = $user;
            return new JsonResponse($response, Response::HTTP_OK);

        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }
}
