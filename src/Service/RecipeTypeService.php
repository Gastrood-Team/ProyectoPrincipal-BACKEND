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

    public function createRecipeType(array $data, UploadedFile $imageFile) : RecipeType {

        $uploadedFile = $this->cloudinary->uploadApi()->upload($imageFile->getRealPath());

        $recipeType = new RecipeType();
        $recipeType->setRecipeTypeName($data['recipeTypeName']);
        $recipeType->setRecipeTypeImage($uploadedFile['url']);

        // $this->recipeTypeRepository->save($recipeType, true);

        return $recipeType;
    }

    public function getRecipeTypes() : array 
    {
        $result = [];
        $recipeTypes = $this->recipeTypeRepository->findAll();

        foreach ($recipeTypes as $type) {
            $result[] = [
                'recipeTypeId' => $type->getId(),
                'recipeTypeName' => $type->getRecipeTypeName(),
                'recipeTypeImage' => $type->getRecipeTypeImage()
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