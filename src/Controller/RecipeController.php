<?php

namespace App\Controller;

use App\Service\RecipeService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RecipeController extends AbstractController
{

    private $recipeService;
    private $logger;
    private  $normalizer;

    public function __construct(NormalizerInterface $normalizer, RecipeService $recipeService, LoggerInterface $logger){
        $this->normalizer = $normalizer;
        $this->recipeService = $recipeService;
        $this->logger = $logger;
    }
    
    #[Route('/recipes', name: 'get-recipes',methods:'GET')]
    public function listRecipes(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $recipeType = $request->query->get('type');

        try {

            $recipes = $this->recipeService->getPaginatedRecipesByType($page, $recipeType);

        } catch (\Exception $e) {        
        
            $this->logger->error($e->getMessage());

            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while loading the recipes, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        $response['status'] = 'success';
        $response['data'] = $recipes;
        return new JsonResponse($response, Response::HTTP_OK);

    }

    #[Route('recipes/{id}', name: 'get-recipe', methods:'GET')]
    public function showRecipe(int $id): JsonResponse
    {
        try {

            $recipe = $this->recipeService->getRecipeDetails($id);

            if($recipe === null){
                $response['status'] = 'error';
                $response['message'] = "The recipe you're trying to access was not found";
                return new JsonResponse($response, Response::HTTP_NOT_FOUND);
            }

        } catch (\Exception $e) {

            $this->logger->error($e->getMessage());

            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while loading the recipe details, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        }
        $response['status'] = 'success';
        $response['data'] = $recipe;
        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/recipes', name: 'crete_recipe', methods:'POST')]
    public function create(Request $request): JsonResponse
    { 
        $data = $request->request->all();
        $file = $request->files->get('recipeImage');  

        if ($file && !in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            $response['status'] = 'error';
            $response['message'] = "The file you're prividing is not a valid format";
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        try {

            $recipe = $this->recipeService->createRecipe($data,$file);

        } catch (\Exception $e) {

            $this->logger->error($e->getMessage());

            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while creating the recipe, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response['status'] = 'created';
        $response['message'] = 'Your recipe has been created';
        $response['data'] = $this->normalizer->normalize($recipe);
        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    #[Route('/recipes/{id}', name:'update-recipe', methods:'POST')] // PUT
    public function update(Request $request, int $id): JsonResponse{

        $data = $request->request->all();
        $file = $request->files->get('recipeImage');

        if (!$file || !in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
            $response['status'] = 'error';
            $response['message'] = "The file you're prividing is not a valid format";
            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        try {
            
            $updatedRecipe = $this->recipeService->updateRecipe($data, $file, $id);

        } catch (\Exception $e) {
            
            $this->logger->error($e->getMessage());
            
            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while updating the recipe, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
            
        }  
        $response['status'] = 'success';
        $response['data'] = $this->normalizer->normalize($updatedRecipe);
        return new JsonResponse($response, Response::HTTP_OK);
        
    }

    #[Route('/recipes/{id}', name:'delete-recipe', methods:'DELETE')]
    public function destroy($id): JsonResponse 
    {

        try {

            $this->recipeService->deleteRecipebyId($id);
        
        } catch (\Exception $e) {
            
            $this->logger->error($e->getMessage());

            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while deleting the recipe, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);

        }

        $response['status'] = 'success';
        $response['message'] = 'Your recipe has been deleted';
        return new JsonResponse($response, Response::HTTP_OK);
    }
}
