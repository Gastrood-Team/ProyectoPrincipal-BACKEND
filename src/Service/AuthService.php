<?php

namespace App\Service;

use App\Entity\Profile;
use App\Entity\User;
use App\Repository\ProfileRepository;
use App\Repository\UserRepository;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService{

    private $_userRepository;
    private $_passwordHasher;
    private $_jwtManger;
    private $_profileRepository;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager, ProfileRepository $profileRepository){
        $this->_userRepository = $userRepository;
        $this->_passwordHasher = $passwordHasher;
        $this->_jwtManger = $jwtManager;
        $this->_profileRepository = $profileRepository;
    }

    public function register(array $data): void
    {
        
        $email = $this->_userRepository->findOneBy(['email' => $data['email']]);
        $username = $this->_profileRepository->findOneBy(['username' => $data['username']]);
        
        if($email){
            throw new Exception("The email is already in use", 409);
        }

        if($username){
            throw new Exception("The username is already in use", 409);
        }

        $user = new User();
        $profile = new Profile();
        
        $profile->setUsername($data['username']);
        $profile->setFirstName($data['firstName']);
        $profile->setLastname($data['lastName']);
        
        $user->setEmail($data['email']);
        $user->setPassword($this->_passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(["ROLE_USER"]);
        $user->setProfile($profile);

        $this->_profileRepository->save($profile, true);        
        $this->_userRepository->save($user, true);        
    }
    
    public function login(array $data) : JsonResponse
    {
        $email = $data['email'];
        $password = $data['password'];

        $user = $this->_userRepository->findOneBy(['email' => $email]);

        if(!$user){
            throw new Exception("Email is not registered", 400);
        }
        
        if (!$this->_passwordHasher->isPasswordValid($user, $password)) {
            throw new Exception("Incorrect password", 401);
        }


        $token = $this->_jwtManger->create($user);

        return new JsonResponse(['token' => $token]);
    }
}

?>