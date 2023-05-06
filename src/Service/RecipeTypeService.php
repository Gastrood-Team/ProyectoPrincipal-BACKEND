<?php

namespace App\Service;

use App\Entity\RecipeType;
use App\Repository\RecipeTypeRepository;
use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RecipeTypeService
{

    private $cloudinary;
    private $recipeTypeRepository;

    public function __construct(RecipeTypeRepository $recipeTypeRepository, Cloudinary $cloudinary)
    {
        $this->recipeTypeRepository = $recipeTypeRepository;
        $this->cloudinary = $cloudinary;
    }

    public function createRecipeType(array $data, UploadedFile $imageFile): array
    {

        $uploadedFile = $this->cloudinary->uploadApi()->upload($imageFile->getRealPath());

        $recipeType = new RecipeType();
        $recipeType->setName($data['name']);
        $recipeType->setImageId($uploadedFile['public_id']);
        $recipeType->setImageUrl($uploadedFile['url']);

        $this->recipeTypeRepository->save($recipeType, true);

        $result = [
            'id' => $recipeType->getId(),
            'name' => $recipeType->getName(),
            'image' => $recipeType->getImageUrl()
        ];

        return $result;
    }

    public function getRecipeTypes(): array
    {
        $result = [];
        $recipeTypes = $this->recipeTypeRepository->findAll();

        foreach ($recipeTypes as $type) {
            $result[] = [
                'id' => $type->getId(),
                'name' => $type->getName(),
                'image' => $type->getImageUrl()
            ];
        }

        return $result;
    }

    public function deleteRecipeTypeById($id)
    {
        $recipeType = $this->recipeTypeRepository->find($id);
        $this->cloudinary->uploadApi()->destroy($recipeType->getImageId());
        $this->recipeTypeRepository->remove($recipeType, true);
    }
}
