<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdvertRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"title"},
 *     message="Ce titre a déjà été choisi, veuillez choisir un autre."
 * )
 */
class Advert
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "Le titre doit avoir au moins {{ limit }} caractères",
     *      maxMessage = "Le titre ne doit pas dépasser {{ limit }} caractères",
     *      allowEmptyString = false
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     * @Assert\GreaterThanOrEqual(
     *     value = 0,
     *     message="Le prix ne peut être négatif !"
     * )
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min = 25,
     *      minMessage = "La description globale doit avoir au moins {{ limit }} caractères",
     *      allowEmptyString = false
     * )
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(
     *      min = 100,
     *      minMessage = "La description détaillée doit avoir au moins {{ limit }} caractères",
     *      allowEmptyString = false
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(
     *    message = "L'url '{{ value }}' n'est pas un url valide",
     * )
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(
     *     value = 1,
     *     message="Le nombre de chambres doit être supérieur ou égal à 1 !"
     * )
     */
    private $rooms;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="advert", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="adverts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="advert", orphanRemoval=false)
     */
    private $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="advert", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * Advert constructor.
     */
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->title.' '.$this->slug.' '.$this->introduction.' '.$this->content.' '.$this->coverImage;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function initializeSlug(): void
    {
        if (empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->title);
        }
    }

    /**
     * @return array DateTime object array of unavailable days
     */
    public function getUnavailableDays(): array
    {
        $unavailableDays = [];

        foreach ($this->bookings as $booking) {
            // Get booking days in Timestamp[]
            $result = range(
                $booking->getStartDate()->getTimestamp(),
                $booking->getEndDate()->getTimestamp(),
                24*60*60
            );
            // Transform booking days in DateTime[]
            $days = array_map(static function ($dayTimestamp){
                return new \DateTime(date('Y-m-d', $dayTimestamp));
            }, $result);

            $unavailableDays = array_merge($unavailableDays, $days);
        }

        return $unavailableDays;
    }

    /**
     * @return float|int
     */
    public function getAverageRating()
    {
        //Get rating sum
        $sum = array_reduce($this->comments->toArray(), static function ($total, $comment){
            return $total + $comment->getRating();
        }, 0);

        //Get rating average
        if (count($this->comments) > 0) {
            return $sum / count($this->comments);
        }

        return 0;
    }

    /**
     * @param User $author
     * @return mixed|null
     */
    public function getCommentFromAuthor(User $author)
    {
        foreach ($this->comments as $comment) {
            if ($comment->getAuthor() === $author){
                return $comment;
            }
        }

        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
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

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAdvert($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAdvert() === $this) {
                $image->setAdvert(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setAdvert($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getAdvert() === $this) {
                $booking->setAdvert(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAdvert($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAdvert() === $this) {
                $comment->setAdvert(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
