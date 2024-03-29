<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\BlogPost;
use App\Repository\CommentRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *  attributes={
 *      "order"={"published": "DESC"},
 *      "pagination_client_enabled"=true,
 *      "pagination_client_items_per_page"=true
 *  },
 *  itemOperations={
 *      "get",
 *      "put"={
 *          "access_control"="is_granted('ROLE_EDITOR') or (is_granted('ROLE_COMMENTATOR') and object.getAuthor() == user)"
 *      }
 *  },
 *  collectionOperations={
 *      "get",
 *      "post"={
 *          "access_control"="is_granted('ROLE_COMMENTATOR')",
 *          "normalization_context"={
 *              "groups"={"get-comment-with-author"}
 *          }
 *      },
 *  },
 *  subresourceOperations={
 *      "api_blog_posts_comments_get_subresource"={
 *          "normalization_context"={
 *              "groups"={"get-comment-with-author"}
 *          }
 *      }
 *  },
 *  denormalizationContext={
 *      "groups"={
 *          "post"
 *      }
 *  }
 * )
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 */
class Comment implements AuthoredEntityInterface, PublishedDateEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get-comment-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"post", "get-comment-with-author"})
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=3000)
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"get-comment-with-author"})
     */
    private $published;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get-comment-with-author"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"post"})
     */
    private $blogPost;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublished(): ?DateTimeInterface
    {
        return $this->published;
    }

    public function setPublished(DateTimeInterface $published): PublishedDateEntityInterface
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get the value of author
     *
     * @return UserInterface
     */
    public function getAuthor(): ?UserInterface
    {
        return $this->author;
    }

    /**
     * Set the value of author
     *
     * @param UserInterface $author
     *
     * @return self
     */
    public function setAuthor(UserInterface $author): AuthoredEntityInterface
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get the value of blogPost
     */
    public function getBlogPost(): ?BlogPost
    {
        return $this->blogPost;
    }

    /**
     * Set the value of blogPost
     *
     * @return  self
     */
    public function setBlogPost($blogPost): self
    {
        $this->blogPost = $blogPost;

        return $this;
    }

    public function __toString(): string
    {
        return substr($this->content, 0, 20) . '...';
    }
}
