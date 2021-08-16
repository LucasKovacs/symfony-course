<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $blogPost = new BlogPost;
        $blogPost->setTitle('A first post!');
        $blogPost->setPublished(new \DateTime('2018-07-01 12:00:00'));
        $blogPost->setContent('Post text!');
        $blogPost->setAuthor('Lucas');
        $blogPost->setSlug('a-first-post');

        $manager->persist($blogPost);

        $blogPost = new BlogPost;
        $blogPost->setTitle('A second post!');
        $blogPost->setPublished(new \DateTime('2019-08-16 12:00:00'));
        $blogPost->setContent('Post text second!');
        $blogPost->setAuthor('Lucas');
        $blogPost->setSlug('a-second-post');

        $manager->persist($blogPost);

        $manager->flush(); // execute / save
    }
}
