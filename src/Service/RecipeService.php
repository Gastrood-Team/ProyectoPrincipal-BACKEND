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
        $recipe->setName($data['name']);
        $recipe->setDescription($data['description']);
        $recipe->setImage($uploadedFile['public_id']);

        foreach ($data['typesId'] as $id) {
            $recipeType = $this->recipeTypeRepository->find($id);
            $recipe->addType($recipeType);
        }

        $this->recipeRepository->save($recipe, true);

        // Circular Reference Prevention

        $result = [
            'id' => $recipe->getId(),
            'name' => $recipe->getName(),
            'description' => $recipe->getDescription(),
            'image' => $this->cloudinary->adminApi()->asset($recipe->getImage())['url']
        ];

        foreach ($recipe->getTypes() as $recipeType) {
            $recipeTypeData = [
                'id' => $recipeType->getId(),
                'name' => $recipeType->getRecipeTypeName(),
            ];

            $result['types'][] = $recipeTypeData;
        }

        return $result;
    }

    public function getPaginatedRecipesByType(int $page, string $recipeType): array
    {
        $result = [];
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Retrieves recipes based of the recipe type
        $queryBuilder = $this->recipeRepository->createQueryBuilder('recipe')
            ->join('recipe.types', 'types')
            ->where('types.recipeTypeName = :type')
            ->setParameter('type', $recipeType)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('recipe.id', 'ASC');

        $recipes = $queryBuilder->getQuery()->getResult();
        // $recipes = $this->recipeRepository->findBy([], [], $limit, $offset);

        foreach ($recipes as $recipe) {
            $result[] = [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'description' => $recipe->getDescription(),
                'Image' => $this->cloudinary->adminApi()->asset($recipe->getImage())['url']
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
            'image' => $this->cloudinary->adminApi()->asset($recipe->getImage())['url']
        ];

        foreach ($recipe->getTypes() as $recipeType) {
            $recipeTypeData = [
                'id' => $recipeType->getId(),
                'name' => $recipeType->getRecipeTypeName(),
            ];

            $result['types'][] = $recipeTypeData;
        }

        return $result;
    }

    public function updateRecipe(array $data, UploadedFile $file, int $id): array
    {
        $recipe = $this->recipeRepository->find($id);

        if (isset($data['name'])) {
            $recipe->setName($data['name']);
        }
        if (isset($data['description'])) {
            $recipe->setDescription($data['description']);
        }

        if (isset($data['typesId'])) {
            $recipe->removeAllTypes();
            foreach ($data['typesId'] as $id) {
                $recipeType = $this->recipeTypeRepository->find($id);
                $recipe->addType($recipeType);
            }
        }

        if ($file) {
            $this->cloudinary->uploadApi()->destroy($recipe->getImage());
            $uploadedFile = $this->cloudinary->uploadApi()->upload($file->getRealPath());
            $recipe->setImage($uploadedFile['public_id']);
        }

        $this->recipeRepository->save($recipe, true);

        $result = [
            'id' => $recipe->getId(),
            'name' => $recipe->getName(),
            'description' => $recipe->getDescription(),
            'image' => $this->cloudinary->adminApi()->asset($recipe->getImage())['url']
        ];

        foreach ($recipe->getTypes() as $recipeType) {
            $recipeTypeData = [
                'id' => $recipeType->getId(),
                'name' => $recipeType->getRecipeTypeName(),
            ];

            $result['types'][] = $recipeTypeData;
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
