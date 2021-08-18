<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *  itemOperations={
 *      "get"={
 *          "access_control"="is_granted('IS_AUTHENTICATED_FULLY')",
 *          "normalization_context"={
 *              "groups"={"get"}
 *          }
 *      },
 *      "put"={
 *          "access_control"="is_granted('IS_AUTHENTICATED_FULLY') and object.getAuthor() == user",
 *          "denormalization_context"={
 *              "groups"={"put"}
 *          },
 *          "normalization_context"={
 *              "groups"={"get"}
 *          }
 *      }
 *  },
 *  collectionOperations={
 *      "post"={
 *          "denormalization_context"={
 *              "groups"={"post"}
 *          },
 *          "normalization_context"={
 *              "groups"={"get"}
 *          }
 *      }
 *  }
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    const ROLE_COMMENTATOR = 'ROLE_COMMENTATOR';
    const ROLE_WRITER = 'ROLE_WRITER';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    const DEFAULT_ROLES = [self::ROLE_COMMENTATOR];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get", "post", "get-comment-with-author", "get-blog-post-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=6, max=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"put", "post"})
     * @Assert\NotBlank()
     * @Assert\Regex(
     *  pattern="/(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{7,}/",
     *  message="Password must be 7 characters long and contain at least one digit, one uppercase letter and one lowercase letter",
     * )
     */
    private $password;

    /**
     * @Groups({"put", "post"})
     * @Assert\NotBlank()
     * @Assert\Expression(
     *  "this.getPassword() === this.getRetypedPassword()",
     *  message="Password does not match",
     * )
     */
    private $retypedPassword;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get", "post", "put", "get-comment-with-author", "get-blog-post-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"put", "post", "get-admin", "get-owner"})
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(min=6, max=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="author")
     * @Groups({"get"})
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     * @Groups({"get"})
     */
    private $comments;

    /**
     * @ORM\Column(type="simple_array", length=200)
     * @Groups({"get-admin", "get-owner"})
     */
    private $roles;

    public function __construct()
    {
        $this->posts = new ArrayCollection;
        $this->comments = new ArrayCollection;
        $this->roles = self::DEFAULT_ROLES;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of posts
     *
     * @return Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set the value of posts
     *
     * @return  self
     */
    public function setPosts($posts): self
    {
        $this->posts = $posts;

        return $this;
    }

    /**
     * Get the value of comments
     *
     * @return Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set the value of comments
     *
     * @return self
     */
    public function setComments($comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get the value of roles
     *
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * Set the value of roles
     *
     * @param array $roles
     * @return self
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }

    /**
     * Get the value of retyped password
     */
    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    /**
     * Set the value of retyped password
     *
     * @return  self
     */
    public function setRetypedPassword($retypedPassword): self
    {
        $this->retypedPassword = $retypedPassword;

        return $this;
    }
}
