<?php

namespace App\Service;

use App\Repository\UserRepository;
use Cloudinary\Cloudinary;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserService{

    private $_userRepository;
    private $_tokenStorage;
    private $cloudinary;

    public function __construct(Cloudinary $cloudinary, UserRepository $userRepository, TokenStorageInterface $tokenStorage){
        $this->_userRepository = $userRepository;
        $this->_tokenStorage = $tokenStorage;
        $this->cloudinary = $cloudinary;
    }

    public function getLoggedUser(): array
    {
        $email = $this->_tokenStorage->getToken()->getUserIdentifier();
        $user = $this->_userRepository->findOneBy(['email' => $email]);
        $profile = $user->getProfile();

        $result = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'profileId' => $profile->getId(),
            'username' => $profile->getUsername(),
        ];

        if($profile->getProfileImageUrl() != null){
            $result = ['profilePic' => $profile->getProfileImageUrl()];
        }
        return $result;
    }
}

?>