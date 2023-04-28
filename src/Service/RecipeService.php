<?php

namespace App\Service;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use App\Repository\RecipeTypeRepository;
use App\Repository\UserRepository;
use Cloudinary\Cloudinary;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecipeService
{

    private $recipeRepository;
    private $recipeTypeRepository;
    private $cloudinary;
    private $tokenStorage;
    private $userRepository;

    public function __construct(
        RecipeRepository $recipeRepository, 
        RecipeTypeRepository $recipeTypeRepository, 
        UserRepository $userRepository,
        Cloudinary $cloudinary, 
        TokenStorageInterface $tokenStorage)
    {
        $this->recipeRepository = $recipeRepository;
        $this->recipeTypeRepository = $recipeTypeRepository;
        $this->cloudinary = $cloudinary;
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
    }

    public function createRecipe(array $data, UploadedFile $imageFile): array
    {
        $email = $this->tokenStorage->getToken()->getUserIdentifier();
        $user = $this->userRepository->findOneBy(['email' => $email]);
        $profile = $user->getProfile();

        $uploadedFile = $this->cloudinary->uploadApi()->upload($imageFile->getRealPath());

        $recipe = new Recipe();
        $recipe->setName($data['name']);
        $recipe->setDescription($data['description']);
        $recipe->setImage($uploadedFile['public_id']);
        $recipe->setProfile($profile);

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

        if($recipe === null){
            throw new Exception("The recipe you're trying to access was not found", 404);
        }

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
