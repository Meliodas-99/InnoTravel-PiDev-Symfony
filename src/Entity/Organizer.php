<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Event;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Organizer
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The name can't be empty.")]
    private string $name;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The contact info can't be empty.")]
    #[Assert\Regex(
        pattern: "/^(\d{9}|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/",
        message: "The contact info must be a valid email or a 9-digit number."
    )]
    private string $contact_info;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The website URL can't be empty.")]
    #[Assert\Url(message: "Please enter a valid URL (e.g., https://example.com).")]
    #[Assert\Regex(
    pattern: "/\.com$/i",
    message: "The website URL must be valid."
)]
    #[Assert\Url(message: "Please enter a valid URL (e.g., https://example.com).")]
    private string $website_url;

    #[ORM\Column(type: "boolean")]
    private bool $verified;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "The type is required.")]
    private ?string $type = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "The yaers of experience can't be empty.")]
    #[Assert\GreaterThan(0, message: "choose a valid number.")]
    private ?int $yearsOfExperience = null;

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

    public function getContactInfo()
    {
        return $this->contact_info;
    }

    public function setContactInfo($value)
    {
        $this->contact_info = $value;
    }

    public function getWebsiteUrl()
    {
        return $this->website_url;
    }

    public function setWebsiteUrl($value)
    {
        $this->website_url = $value;
    }

    public function getVerified()
    {
        return $this->verified;
    }

    public function setVerified($value)
    {
        $this->verified = $value;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getYearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }

    public function setYearsOfExperience(int $yearsOfExperience): static
    {
        $this->yearsOfExperience = $yearsOfExperience;
        return $this;
    }
    
    #[ORM\OneToMany(mappedBy: "idOrganizer", targetEntity: Event::class)]
    private Collection $events;

        public function getEvents(): Collection
        {
            return $this->events;
        }
    
        public function addEvent(Event $event): self
        {
            if (!$this->events->contains($event)) {
                $this->events[] = $event;
                $event->setIdOrganizer($this);
            }
    
            return $this;
        }
    
        public function removeEvent(Event $event): self
        {
            if ($this->events->removeElement($event)) {
                // set the owning side to null (unless already changed)
                if ($event->getIdOrganizer() === $this) {
                    $event->setIdOrganizer(null);
                }
            }
    
            return $this;
        }
}
