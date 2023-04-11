<?php

namespace App\Controller;

use App\Service\RecipeTypeService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RecipeTypeController extends AbstractController
{

    private $recipeTypeService;
    private $logger;
    private $normalizer;

    public function __construct(RecipeTypeService $recipeTypeService, LoggerInterface $logger, NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
        $this->recipeTypeService = $recipeTypeService;
        $this->logger = $logger;
    }

    #[Route('/recipetypes', name: 'list-recipe-types', methods:'GET')]
    public function list(): JsonResponse {

        try {

            $recipesTypes = $this->recipeTypeService->getRecipeTypes();

        } catch (\Exception $e) {        
        
            $this->logger->error($e->getMessage());

            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while loading the recipe types, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        $response['status'] = 'success';
        $response['data'] = $recipesTypes;
        return new JsonResponse($response, Response::HTTP_OK);

    }

    #[Route('/recipetypes', name: 'create-recipe-type', methods:'POST')]
    public function create(Request $request): JsonResponse {

        $data = $request->request->all();
        $file = $request->files->get('recipeTypeImage');

        try {

            $recipeType = $this->recipeTypeService->createRecipeType($data, $file);
        
        } catch (\Exception $e) {

            $this->logger->error($e->getMessage());

            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while creating the recipe type, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        $response['status'] = 'success';
        $response['data'] = $this->normalizer->normalize($recipeType);
        return new JsonResponse($response,Response::HTTP_OK);
    }

    #[Route('/recipetypes/{id}', name:'delete-recipe-type', methods:'DELETE')]
    public function destroy($id): JsonResponse 
    {

        try {

            $this->recipeTypeService->deleteRecipeTypebyId($id);
        
        } catch (\Exception $e) {
            
            $this->logger->error($e->getMessage());

            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while deleting the recipe type, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        $response['status'] = 'success';
        $response['message'] = 'Recype type has been deleted';
        return new JsonResponse($response, Response::HTTP_OK);
    }
    
}
