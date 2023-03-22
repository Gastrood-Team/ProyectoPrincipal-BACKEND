<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RecipeService{

    private $recipeRepository;
    private $cloudinary;

    public function __construct(RecipeRepository $recipeRepository, Cloudinary $cloudinary)
    {
        $this->recipeRepository = $recipeRepository;
        $this->cloudinary = $cloudinary;
    }

    public function createRecipe(string $name, string $description, UploadedFile $imageFile) : Recipe {

        $uploadedFile = $this->cloudinary->uploadApi()->upload($imageFile->getRealPath());
        
        $recipe = new Recipe();
        $recipe->setName($name);
        $recipe->setDescription($description);
        $recipe->setImage($uploadedFile['public_id']);
        
        // $this->recipeRepository->save($recipe, true);
        
        return $recipe;
    }
}
?>