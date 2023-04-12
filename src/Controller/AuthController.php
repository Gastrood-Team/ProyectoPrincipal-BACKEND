<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/signup', name: 'signup', methods:'POST')]
    public function sigup(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'signup'
        ]);
    }

    #[Route('/login', name: 'login', methods:'POST')]
    public function login(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'Login'
        ]);
    }
    
    #[Route('/logout', name: 'logout', methods:'POST')]
    public function logout(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'out'
        ]);
    }
    
    #[Route('/reset-password', name: 'reset', methods:'POST')]
    public function resetPassword(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'reset'
        ]);
    }    
}
