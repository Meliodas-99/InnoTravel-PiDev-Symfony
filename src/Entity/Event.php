<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Validator\Constraints as Assert;

use App\Entity\Organizer;

#[ORM\Entity]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The name can't be empty.")]
    private string $name;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The description can't be empty.")]
    #[Assert\Length(max: 255, maxMessage: "The description can't be over 255 characters.")]
    private string $description;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The location can't be empty.")]
    #[Assert\Regex(pattern: "/^([A-Za-z\s]+,\s*){1,2}[A-Za-z\s]+$/",
        message: "Please enter a valid location in the format 'City, Country' or 'City, Region, Country'."
    )]
    private string $location;

    #[ORM\Column(type: "date")]
    #[Assert\NotBlank(message: "The date can't be empty.")]
    #[Assert\GreaterThanOrEqual("today", message: "The date can't be in the past, choose a correct date.")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The category is required")]
    private string $category;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "The price can't be empty.")]
    #[Assert\GreaterThan(0, message: "The price should not be a negative number.")]
    private float $price;

        #[ORM\ManyToOne(targetEntity: Organizer::class, inversedBy: "events")]
    #[ORM\JoinColumn(name: 'idOrganizer', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Organizer $idOrganizer;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($value)
    {
        $this->location = $value;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($value)
    {
        $this->date = $value;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($value)
    {
        $this->category = $value;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($value)
    {
        $this->price = $value;
    }

    public function getIdOrganizer()
    {
        return $this->idOrganizer;
    }

    public function setIdOrganizer($value)
    {
        $this->idOrganizer = $value;
    }
}
