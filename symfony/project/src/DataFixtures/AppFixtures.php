<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var Faker\Factory
     */
    private $faker;

    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@blog.com',
            'name' => 'Piotr Jura',
            'password' => 'secret123#',
            'roles' => [User::ROLE_SUPERADMIN],
            'enabled' => 1,
        ],
        [
            'username' => 'john_doe',
            'email' => 'john@blog.com',
            'name' => 'John Doe',
            'password' => 'secret123#',
            'roles' => [User::ROLE_ADMIN],
            'enabled' => 1,
        ],
        [
            'username' => 'rob_smith',
            'email' => 'rob@blog.com',
            'name' => 'Rob Smith',
            'password' => 'secret123#',
            'roles' => [User::ROLE_WRITER],
            'enabled' => 1,
        ],
        [
            'username' => 'jenny_rowling',
            'email' => 'jenny@blog.com',
            'name' => 'Jenny Rowling',
            'password' => 'secret123#',
            'roles' => [User::ROLE_WRITER],
            'enabled' => 1,
        ],
        [
            'username' => 'han_solo',
            'email' => 'han@blog.com',
            'name' => 'Han Solo',
            'password' => 'secret123#',
            'roles' => [User::ROLE_EDITOR],
            'enabled' => 0,
        ],
        [
            'username' => 'jedi_knight',
            'email' => 'jedi@blog.com',
            'name' => 'Jedi Knight',
            'password' => 'secret123#',
            'roles' => [User::ROLE_COMMENTATOR],
            'enabled' => 1,
        ],
    ];

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGenerator $tokenGenerator)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
        $this->tokenGenerator = $tokenGenerator;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadBlogPosts($manager);
        $this->loadComments($manager);
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $user) {
            $userEntity = new User;
            $userEntity->setUsername($user['username']);
            $userEntity->setEmail($user['email']);
            $userEntity->setName($user['name']);
            $userEntity->setPassword($this->passwordEncoder->encodePassword($userEntity, $user['password']));
            $userEntity->setRoles($user['roles']);
            $userEntity->setEnabled($user['enabled']);

            if (!$user['enabled']) {
                $userEntity->setConfirmationToken($this->tokenGenerator->getRandomSecureToken());
            }

            $this->addReference('user_' . $user['username'], $userEntity);

            $manager->persist($userEntity);
        }

        $manager->flush(); // execute / save
    }

    public function loadBlogPosts(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            $blogPost = new BlogPost;
            $blogPost->setTitle($this->faker->realText(30));
            $blogPost->setPublished($this->faker->dateTime);
            $blogPost->setContent($this->faker->realText());
            $blogPost->setAuthor($this->getRandomUserReference($blogPost));
            $blogPost->setSlug($this->faker->slug);

            $this->addReference('blog_posts_' . $i, $blogPost);

            $manager->persist($blogPost);
        }

        $manager->flush(); // execute / save
    }

    public function loadComments(ObjectManager $manager)
    {
        for ($i = 0; $i < 100; $i++) {
            for ($j = 0; $j < rand(1, 10); $j++) {
                $comment = new Comment;
                $comment->setContent($this->faker->realText());
                $comment->setPublished($this->faker->dateTimeThisYear);
                $comment->setAuthor($this->getRandomUserReference($comment));
                $comment->setBlogPost($this->getReference('blog_posts_' . $i));

                $manager->persist($comment);
            }
        }

        $manager->flush(); // execute / save
    }

    /**
     *
     *
     * @return User
     */
    protected function getRandomUserReference($entity): User
    {
        $randomUser = self::USERS[rand(0, 5)];

        if ($entity instanceof BlogPost && !count(array_intersect(
            $randomUser['roles'],
            [
                User::ROLE_SUPERADMIN,
                User::ROLE_ADMIN,
                User::ROLE_WRITER,
            ]
        ))) {
            return $this->getRandomUserReference($entity);
        }

        if ($entity instanceof Comment && !count(array_intersect(
            $randomUser['roles'],
            [
                User::ROLE_SUPERADMIN,
                User::ROLE_ADMIN,
                User::ROLE_WRITER,
                User::ROLE_COMMENTATOR,
            ]
        ))) {
            return $this->getRandomUserReference($entity);
        }

        return $this->getReference('user_' . $randomUser['username']);
    }
}
