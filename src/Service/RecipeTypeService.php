<?php

namespace App\Service;

use App\Entity\RecipeType;
use App\Repository\RecipeTypeRepository;
use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RecipeTypeService{

    private $cloudinary;
    private $recipeTypeRepository;

    public function __construct(RecipeTypeRepository $recipeTypeRepository, Cloudinary $cloudinary)
    {
        $this->recipeTypeRepository = $recipeTypeRepository;
        $this->cloudinary = $cloudinary;
    }

    public function createRecipeType(array $data, UploadedFile $imageFile) : array {

        $uploadedFile = $this->cloudinary->uploadApi()->upload($imageFile->getRealPath());

        $recipeType = new RecipeType();
        $recipeType->setRecipeTypeName($data['name']);
        $recipeType->setRecipeTypeImage($uploadedFile['public_id']);

        $this->recipeTypeRepository->save($recipeType, true);

        $result = [
            'id' => $recipeType->getId(),
            'name' => $recipeType->getRecipeTypeName(),
            'image' => $this->cloudinary->adminApi()->asset($recipeType->getRecipeTypeImage())['url']
        ];

        return $result;
    }

    public function getRecipeTypes() : array 
    {
        $result = [];
        $recipeTypes = $this->recipeTypeRepository->findAll();

        foreach ($recipeTypes as $type) {
            $result[] = [
                'id' => $type->getId(),
                'name' => $type->getRecipeTypeName(),
                'image' => $this->cloudinary->adminApi()->asset($type->getRecipeTypeImage())['url']
            ];
        }

        return $result;
    }

    public function deleteRecipeTypeById($id){
        $recipeType = $this->recipeTypeRepository->find($id);
        $this->cloudinary->uploadApi()->destroy($recipeType->getRecipeTypeImage());
        $this->recipeTypeRepository->remove($recipeType,true);
    }

}

?>