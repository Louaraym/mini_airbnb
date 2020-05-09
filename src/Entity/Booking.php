<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $guest;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Advert", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $advert;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type(
     *     type="\DateTimeInterface",
     *     message="La date d'arrivée doit être au au format jj/mm/aaaa"
     * )
     * @Assert\GreaterThan(
     *     "today",
     *     message="La date d'arrivée doit être ultérieure à la date d'aujourd'hui",
     *     groups={"front_validation"}
     * )
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type(
     *     type="\DateTimeInterface",
     *     message="La date de départ doit être au format jj/mm/aaaa"
     * )
     * @Assert\GreaterThan(
     *     propertyPath="startDate",
     *     message="La date de départ doit être ultérieure à la date d'arrivée"
     * )
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function calculateBookingTotalAmount(): void
    {
        if (empty($this->amount)){
            $this->amount = $this->advert->getPrice()*$this->getStayDuration();
        }
    }

    /**
     * @return mixed
     */
    public function getStayDuration()
    {
        return $this->endDate->diff($this->startDate)->days;
    }


    /**
     * @return bool
     */
    public function isBookableDates(): bool
    {
        // Get available days
        $unavailableDays = $this->advert->getUnavailableDays();
        // Get booking days
        $bookingDays = $this->getBookingDays();
        //Format days in string
        $formatDayInString = static function ($day){
            return $day->format('Y-m-d');
        };

        $stringBookingDays = array_map($formatDayInString, $bookingDays);
        $stringUnavailableDays = array_map($formatDayInString, $unavailableDays);

        //Check if a booking day is available
        foreach ($stringBookingDays as $stringBookingDay){
            if (in_array($stringBookingDay, $stringUnavailableDays, true)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return DateTime[]
     */
    public function getBookingDays(): array
    {
        // Get booking days in Timestamp[]
        $result = range(
            $this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24*60*60
        );
        // Transform booking days in DateTime[] and return it
        return array_map(static function ($dayTimestamp){
            return new DateTime(date('Y-m-d', $dayTimestamp));
        }, $result);
    }

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuest(): ?User
    {
        return $this->guest;
    }

    public function setGuest(?User $guest): self
    {
        $this->guest = $guest;

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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
