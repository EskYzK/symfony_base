<?php

namespace App\Entity;

use App\Repository\CandidateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
class Candidate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?bool $hasExperience = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Expression(
        "this.hasExperience() === false or (this.getExperienceDetails() !== null and this.getExperienceDetails() !== '')",
        message: "Les détails d'expérience sont obligatoires si vous avez de l'expérience."
    )]
    private ?string $experienceDetails = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $availabilityDate = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $currentStep = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function hasExperience(): ?bool
    {
        return $this->hasExperience;
    }

    public function setHasExperience(bool $hasExperience): static
    {
        $this->hasExperience = $hasExperience;

        // Si pas d'expérience, on vide automatiquement les détails
        if ($hasExperience === false) {
            $this->experienceDetails = null;
        }

        return $this;
    }

    public function getExperienceDetails(): ?string
    {
        return $this->experienceDetails;
    }

    public function setExperienceDetails(?string $experienceDetails): static
    {
        $this->experienceDetails = $experienceDetails;

        return $this;
    }

    public function getAvailabilityDate(): ?\DateTime
    {
        return $this->availabilityDate;
    }

    public function setAvailabilityDate(\DateTime $availabilityDate): static
    {
        $this->availabilityDate = $availabilityDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCurrentStep(): ?string
    {
        return $this->currentStep;
    }

    public function setCurrentStep(?string $currentStep): static
    {
        $this->currentStep = $currentStep;
        return $this;
    }
}
