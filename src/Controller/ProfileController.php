<?php

namespace App\Controller;

use App\Service\ProfileService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ProfileController extends AbstractController
{
    private $_profileService;

    public function __construct(ProfileService $profileService){
        $this->_profileService = $profileService;
    }

    #[Route('profile/{username}', name: 'get-profile', methods:'GET')]
    public function getProfile(string $username): JsonResponse
    {
        try {

            $profile = $this->_profileService->getProfile($username);
            
            $response['status'] = 'success';
            $response['data'] = $profile;
            return new JsonResponse($response, Response::HTTP_OK);

        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        }
    }

    #[Route('profile/{username}', name: 'edit-profile', methods:'POST')]
    public function update(Request $request, string $username): JsonResponse
    {

        $data = $request->request->all();
        $profileFile = $request->files->get('profileImg');
        $bannerFile = $request->files->get('bannerImg');

        if (!$profileFile || !$bannerFile || !in_array($profileFile->getClientOriginalExtension(), ['jpg', 'jpeg', 'png']) || !in_array($bannerFile->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            $response['status'] = 'error';
            $response['message'] = "The file you're prividing is not a valid format";
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        try {

            $profile = $this->_profileService->update($data, $profileFile, $bannerFile, $username);
            
        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['message'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);    
        }

        $response['status'] = 'success';
        $response['message'] = "Your profile has been updated successfully";
        return new JsonResponse($response, Response::HTTP_OK);
    }

    


}
