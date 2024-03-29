<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\AuthService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AuthController extends AbstractController
{

    private $_authService;
    private $_normalizer;

    public function __construct(AuthService $authService, NormalizerInterface $normalizer)
    {
        $this->_authService = $authService;
        $this->_normalizer = $normalizer;
    }

    #[Route('/signup', name: 'signup', methods:'POST')]
    public function sigup(Request $request, UserRepository $_repository): JsonResponse
    {
        $data = $request->request->all();
        
        try {

            $this->_authService->register($data);

            $response['status'] = 'success';
            $response['message'] = 'Let your culinary adventure begin!';
            return new JsonResponse($response, Response::HTTP_CREATED);
            
        } catch (\Exception $e) {

            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/login', name: 'login', methods:'POST')]
    public function login(Request $request): JsonResponse
    {

        $data = $request->request->all();

        try {

            return $this->_authService->login($data);

        } catch (\Exception $e) {

            $statusCode = $e->getCode() ? : 500;
            $response['status'] = $statusCode;
            $response['message'] = $e->getMessage();
            return new JsonResponse($response, $statusCode);

        }
    }
    
    // #[Route('/logout', name: 'logout', methods:'POST')]
    // public function logout(Request $request): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'out'
    //     ]);
    // }
    
    // #[Route('/reset-password', name: 'reset', methods:'POST')]
    // public function resetPassword(Request $request): JsonResponse
    // {
    //     return $this->json([
    //         'message' => 'reset'
    //     ]);
    // }    
}
