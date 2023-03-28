<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Repository\RecipeTypeRepository;
use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RecipeService
{

    private $recipeRepository;
    private $recipeTypeRepository;
    private $cloudinary;

    public function __construct(RecipeRepository $recipeRepository, RecipeTypeRepository $recipeTypeRepository, Cloudinary $cloudinary)
    {
        $this->recipeRepository = $recipeRepository;
        $this->recipeTypeRepository = $recipeTypeRepository;
        $this->cloudinary = $cloudinary;
    }

    public function createRecipe(array $data, UploadedFile $imageFile): array
    {
        $uploadedFile = $this->cloudinary->uploadApi()->upload($imageFile->getRealPath());

        $recipe = new Recipe();
        $recipe->setName($data['recipeName']);
        $recipe->setDescription($data['recipeDescription']);
        $recipe->setImage($uploadedFile['url']);

        foreach ($data['recipeTypesId'] as $id) {
            $recipeType = $this->recipeTypeRepository->find($id);
            $recipe->addType($recipeType);
        }

        $this->recipeRepository->save($recipe, true);

        // Circular Reference Prevention

        $result = [
            'id' => $recipe->getId(),
            'name' => $recipe->getName(),
            'description' => $recipe->getDescription(),
            'image' => $recipe->getImage(),
        ];

        foreach ($recipe->getTypes() as $recipeType) {
            $recipeTypeData = [
                'id' => $recipeType->getId(),
                'name' => $recipeType->getRecipeTypeName(),
            ];

            $result['recipeTypes'][] = $recipeTypeData;
        }

        return $result;
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

    public function getRecipeDetails(int $id): ?array
    {
        $recipe = $this->recipeRepository->find($id);

        $result = [
            'id' => $recipe->getId(),
            'name' => $recipe->getName(),
            'description' => $recipe->getDescription(),
            'image' => $recipe->getImage(),
        ];

        foreach ($recipe->getTypes() as $recipeType) {
            $recipeTypeData = [
                'id' => $recipeType->getId(),
                'name' => $recipeType->getRecipeTypeName(),
            ];

            $result['recipeTypes'][] = $recipeTypeData;
        }

        return $result;
    }

    public function updateRecipe(array $data, UploadedFile $file, int $id): array
    {
        $recipe = $this->recipeRepository->find($id);

        if (isset($data['recipeName'])) {
            $recipe->setName($data['recipeName']);
        }
        if (isset($data['recipeDescription'])) {
            $recipe->setDescription($data['recipeDescription']);
        }

        if (isset($data['recipeTypesId'])) {
            $recipe->removeAllTypes();
            foreach ($data['recipeTypesId'] as $id) {
                $recipeType = $this->recipeTypeRepository->find($id);
                $recipe->addType($recipeType);
            }
        }

        if ($file) {
            $this->cloudinary->uploadApi()->destroy($recipe->getImage());
            $uploadedFile = $this->cloudinary->uploadApi()->upload($file->getRealPath());
            $recipe->setImage($uploadedFile['url']);
        }

        $this->recipeRepository->save($recipe, true);

        $result = [
            'id' => $recipe->getId(),
            'name' => $recipe->getName(),
            'description' => $recipe->getDescription(),
            'image' => $recipe->getImage(),
        ];

        foreach ($recipe->getTypes() as $recipeType) {
            $recipeTypeData = [
                'id' => $recipeType->getId(),
                'name' => $recipeType->getRecipeTypeName(),
            ];

            $result['recipeTypes'][] = $recipeTypeData;
        }

        return $result;
    }

    public function deleteRecipeById($id)
    {
        $recipe = $this->recipeRepository->find($id);
        $this->cloudinary->uploadApi()->destroy($recipe->getImage());
        $this->recipeRepository->remove($recipe, true);
    }
}
