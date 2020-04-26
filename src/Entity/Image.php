<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
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
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(
     *    message = "L'url '{{ value }}' n'est pas un url valide",
     * )
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "Le titre doit avoir au moins {{ limit }} caractères",
     *      allowEmptyString = false
     * )
     */
    private $caption;

    /**
     * @ORM\ManyToOne(targetEntity="Advert", inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $advert;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getAdvert(): ?Advert
    {
        return $this->advert;
    }

    public function setAdvert(?Advert $advert): self
    {
        $this->advert = $advert;

        return $this;
    }
}
