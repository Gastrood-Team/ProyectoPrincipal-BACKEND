<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RecipeService
{

    private $recipeRepository;
    private $cloudinary;

    public function __construct(RecipeRepository $recipeRepository, Cloudinary $cloudinary)
    {
        $this->recipeRepository = $recipeRepository;
        $this->cloudinary = $cloudinary;
    }

    public function createRecipe(string $name, string $description, UploadedFile $imageFile): Recipe
    {
        $uploadedFile = $this->cloudinary->uploadApi()->upload($imageFile->getRealPath());

        $recipe = new Recipe();
        $recipe->setName($name);
        $recipe->setDescription($description);
        $recipe->setImage($uploadedFile['public_url']);

        $this->recipeRepository->save($recipe, true);

        return $recipe;
    }

    public function getPaginatedRecipesByType(int $page): array
    {
        $result = [];
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $recipes = $this->recipeRepository->findBy([], [], $limit, $offset);

        foreach ($recipes as $recipe) {
            $result[] = [
                'recipeId' => $recipe->getId(),
                'recipeName' => $recipe->getName(),
                'recipeDescription' => $recipe->getDescription(),
                'recipeImage' => $recipe->getImage(),
            ];
        }
        return $result;
    }

    public function getRecipeDetails(int $id): ?Recipe
    {
        return $this->recipeRepository->find($id);
    }

    public function updateRecipe(array $data, UploadedFile $file, int $id): Recipe
    {
        $recipe = $this->recipeRepository->find($id);
        
        if(isset($data['recipeName'])){
            $recipe->setName($data['recipeName']);
        }
        if(isset($data['recipeDescription'])){
            $recipe->setDescription($data['recipeDescription']);
        }
        
        if($file){
            $this->cloudinary->uploadApi()->destroy($recipe->getImage());
            $uploadedFile = $this->cloudinary->uploadApi()->upload($file->getRealPath());
            $recipe->setImage($uploadedFile['url']);
        }
        
        $this->recipeRepository->save($recipe, true);
        return $recipe;
    }

    public function deleteRecipeById($id){
        $recipe = $this->recipeRepository->find($id);
        $this->cloudinary->uploadApi()->destroy($recipe->getImage());
        $this->recipeRepository->remove($recipe,true);
    }
}
