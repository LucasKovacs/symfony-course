<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ResetPasswordAction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var JWTTokenManagerInterface
     */
    private $tokenManager;

    public function __construct(ValidatorInterface $validator, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, JWTTokenManagerInterface $tokenManager)
    {
        $this->validator = $validator;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->entityManager = $entityManager;
        $this->tokenManager = $tokenManager;
    }

    /**
     * $reset = new ResetPasswordAction();
     * $reset()
     *
     * @return void
     */
    public function __invoke(User $data)
    {
        $this->validator->validate($data);

        $data->setPassword($this->userPasswordEncoder->encodePassword($data, $data->getNewPassword()));

        // After password change, old tokens are still valid
        $data->setPasswordChangeDate(time());

        // persist is only called when they are initially created,
        // but when we have an existing entity we do not need persist, doctrine figures that by itself
        $this->entityManager->flush();

        $token = $this->tokenManager->create($data);

        return new JsonResponse(['token' => $token]);

        // Validator is only called after we return the data from this action!
        // Only here it checks for user current password, but we've just modified it!

        // Entity is persisted automatically, only if validation pass
    }
}
