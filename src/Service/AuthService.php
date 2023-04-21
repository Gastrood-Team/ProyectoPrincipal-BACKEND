<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService{

    private $_userRepository;
    private $_passwordHasher;
    private $_jwtManger;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager){
        $this->_userRepository = $userRepository;
        $this->_passwordHasher = $passwordHasher;
        $this->_jwtManger = $jwtManager;
    }

    public function register(array $data): void
    {
        $user = new User();
        
        $user->setEmail($data['email']);
        $user->setPassword($this->_passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(["USER_ROLE"]);

        $this->_userRepository->save($user, true);        
    }
    
    public function login(array $data) : JsonResponse
    {
        $email = $data['email'];
        $password = $data['password'];

        $user = $this->_userRepository->findOneBy(['email' => $email]);

        if (!$user || !$this->_passwordHasher->isPasswordValid($user, $password)) {
            throw new Exception("Incorrect Passowrd", 401);
        }

        $token = $this->_jwtManger->create($user);

        return new JsonResponse(['token' => $token]);
    }
}

?>