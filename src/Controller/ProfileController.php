<?php

namespace App\Controller;

use App\Service\ProfileService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProfileController extends AbstractController
{
    private $_profileService;
    private $_logger;
    private  $_normalizer;

    public function __construct(NormalizerInterface $normalizer, ProfileService $profileService, LoggerInterface $logger){
        $this->_normalizer = $normalizer;
        $this->_profileService = $profileService;
        $this->_logger = $logger;
    }

    #[Route('profile/{id}', name: 'get-profile', methods:'GET')]
    public function getProfile(int $id): JsonResponse
    {
        try {

            $profile = $this->_profileService->getProfile($id);

            if($profile === null){
                $response['status'] = 'error';
                $response['message'] = "The profile you're trying to access was not found";
                return new JsonResponse($response, Response::HTTP_NOT_FOUND);
            }
            
            $response['status'] = 'success';
            $response['data'] = $profile;
            return new JsonResponse($response, Response::HTTP_OK);

        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while loading the profile, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }


}
