<?php

namespace App\Service;

use App\Repository\ProfileRepository;
use Cloudinary\Cloudinary;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
            'lastName' => $profile->getLastName(),
            'biography' => $profile->getBiography(),
            'websiteUrl' => $profile->getWebsiteUrl(),
        ];

        if($profile->getProfileImageUrl()){
            $result['profileImg'] = $profile->getProfileImageUrl();
        }

        if($profile->getBannerImageUrl()){
            $result['bannerImg'] = $profile->getBannerImageUrl();

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

    public function update(array $data, UploadedFile $profileFile = null, UploadedFile $bannerFile = null, string $username): void
    {
        $profile = $this->_profileRepository->findOneBy(['username' => $username]);

        if (isset($data['firstName'])) {
            $profile->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $profile->setLastName($data['lastName']);
        }

        if (isset($data['biography'])) {
            $profile->setBiography($data['biography']);
        }

        if (isset($data['websiteUrl'])) {
            $profile->setWebsiteUrl($data['websiteUrl']);
        }

        if ($profileFile) {
            if($profile->getProfileImageId()){
                $this->_cloudinary->uploadApi()->destroy($profile->getProfileImageId());
            }
            $uploadedFile = $this->_cloudinary->uploadApi()->upload($profileFile->getRealPath());
            $profile->setProfileImageId($uploadedFile['public_id']);
            $profile->setProfileImageUrl($uploadedFile['url']);
        }

        if ($bannerFile) {
            if($profile->getBannerImageId()){
                $this->_cloudinary->uploadApi()->destroy($profile->getBannerImageId());
            }
            $uploadedFile = $this->_cloudinary->uploadApi()->upload($bannerFile->getRealPath());
            $profile->setBannerImageId($uploadedFile['public_id']);
            $profile->setBannerImageUrl($uploadedFile['url']);
        }

        $this->_profileRepository->save($profile, true);
    }


}

?>