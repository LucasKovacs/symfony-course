<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UploadImageAction;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Vich\Uploadable()
 * @ApiResource(
 *  attributes={
 *      "order"={"id": "ASC"},
 *      "formats"={"json", "jsonld", "form"={"multipart/form-data"}}
 *  },
 *  collectionOperations={
 *      "get",
 *      "post"={
 *          "method"="POST",
 *          "path"="/images",
 *          "controller"=UploadImageAction::class,
 *          "defaults"={"_api_receive"=false}
 *      }
 *  },
 *  itemOperations={
 *      "get",
 *      "delete"={
 *          "access_control"="is_granted('ROLE_WRITER')"
 *      }
 *  }
 * )
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="url")
     * @Assert\NotNull()
     */
    private $file;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"get-blog-post-with-author"})
     */
    private $url;

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @return self
     */
    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the value of url
     */
    public function getUrl()
    {
        return '/images/' . $this->url;
    }

    /**
     * Set the value of url
     *
     * @return self
     */
    public function setUrl($url): self
    {
        $this->url = $url;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id . ':' . $this->url;
    }
}
