<?php

namespace App\Service;

use App\Entity\Profile;
use App\Repository\ProfileRepository;
use Cloudinary\Cloudinary;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProfileService{

    private $_profileRepository;
    private $_cloudinary;

    public function __construct(ProfileRepository $profileRepository, Cloudinary $cloudinary)
    {
        $this->_profileRepository = $profileRepository;
        $this->_cloudinary = $cloudinary;
    }

    public function getProfile(int $id): ?array
    {
        $profile = $this->_profileRepository->find($id);

        $result = [
            'id' => $profile->getId(),
            'username' => $profile->getUsername(),
            'firstName' => $profile->getFirstName(),
            'lasttName' => $profile->getLastName(),
        ];

        if($profile->getProfilePic()){
            $result['profilePic'] = $this->_cloudinary->adminApi()->asset($profile->getProfilePic())['url'];
        }

        if($profile->getBannerPic()){
            $result['bannerPic'] = $this->_cloudinary->adminApi()->asset($profile->getBannerPic())['url'];

        }

        foreach ($profile->getRecipes() as $recipe) {
            $recipe = [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'description' => $recipe->getDescription(),
                'image' => $this->_cloudinary->adminApi()->asset($recipe->getImage())['url']
            ];

            $result['recipes'][] = $recipe;
        }

        return $result;
    }

}

?>