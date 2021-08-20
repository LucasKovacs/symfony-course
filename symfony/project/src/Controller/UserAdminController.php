<?php

namespace App\Controller;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends BaseAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Allows applications to modify the entity associated with the item being
     * created while persisting it.
     *
     * @param User $entity
     */
    protected function persistEntity($entity)
    {
        $this->encodeUserPassword($entity);

        parent::persistEntity($entity);
    }

    /**
     * Allows applications to modify the entity associated with the item being
     * edited before updating it.
     *
     * @param User $entity
     */
    protected function updateEntity($entity)
    {
        $this->encodeUserPassword($entity);

        parent::updateEntity($entity);
    }

    /**
     * Encode user password
     *
     * @param User $entity
     * @return void
     */
    private function encodeUserPassword(User $entity): void
    {
        $entity->setPassword(
            $this->passwordEncoder->encodePassword($entity, $entity->getPassword())
        );
    }
}
