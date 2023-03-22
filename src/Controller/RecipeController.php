<?php

namespace App\Controller;

use App\Service\RecipeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RecipeController extends AbstractController
{

    private $serializer;
    private $recipeService;

    public function __construct(SerializerInterface $serializer, RecipeService $recipeService){
        $this->serializer = $serializer;
        $this->recipeService = $recipeService;
    }

    #[Route('/recipes', name: 'app_recipe', methods:['POST'])]
    public function create(Request $request): JsonResponse
    { 
        $name = $request->get('recipeName');
        $description = $request->get('recipeDescription');
        $imageFile = $request->files->get('recipeImage');             

        try {
            $recipe = $this->recipeService->createRecipe($name,$description,$imageFile);
        } catch (\Exception $e) {
            $response['status'] = 'error';
            $response['message'] = 'Something went wrong while creating the recipe, please try again later.';
            $response['error'] = $e->getMessage();
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $response['status'] = 'success';
        $response['message'] = 'Your recipe has been created';
        $response['data'] = $this->serializer->serialize($recipe,'json');
        return new JsonResponse($response, Response::HTTP_CREATED);
    }
}
