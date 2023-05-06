<?php

namespace App\Service;

use App\Repository\ProfileRepository;
use Cloudinary\Cloudinary;
use Exception;

class ProfileService{

    private $_profileRepository;
    private $_cloudinary;

    public function __construct(ProfileRepository $profileRepository, Cloudinary $cloudinary)
    {
        $this->_profileRepository = $profileRepository;
        $this->_cloudinary = $cloudinary;
    }

    public function getProfile(string $username): ?array
    {
        $profile = $this->_profileRepository->findOneBy(['username' => $username]);

        if($profile === null){
            throw new Exception("The profile you're trying to access was not found", 404);
        }

        $result = [
            'id' => $profile->getId(),
            'username' => $profile->getUsername(),
            'firstName' => $profile->getFirstName(),
            'lasttName' => $profile->getLastName(),
        ];

        if($profile->getProfileImageUrl()){
            $result['profilePic'] = $profile->getProfileImageUrl();
        }

        if($profile->getBannerImageUrl()){
            $result['bannerPic'] = $profile->getBannerImageUrl();

        }

        foreach ($profile->getRecipes() as $recipe) {
            $recipe = [
                'id' => $recipe->getId(),
                'name' => $recipe->getName(),
                'description' => $recipe->getDescription(),
                'image' => $recipe->getImageUrl()
            ];

            $result['recipes'][] = $recipe;
        }

        return $result;
    }

}

?>