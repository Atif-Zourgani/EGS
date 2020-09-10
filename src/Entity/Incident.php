<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IncidentRepository")
 */
class Incident
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\StudentReliability", mappedBy="incident")
     */
    private $studentReliability;

    /**
     * @ORM\Column(type="integer")
     */
    private $points;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $incidentType;

    public function __construct()
    {
        $this->studentReliability = new ArrayCollection();
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->name;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|StudentReliability[]
     */
    public function getStudentReliability(): Collection
    {
        return $this->studentReliability;
    }

    public function addStudentReliability(StudentReliability $studentReliability): self
    {
        if (!$this->studentReliability->contains($studentReliability)) {
            $this->studentReliability[] = $studentReliability;
            $studentReliability->addIncident($this);
        }

        return $this;
    }

    public function removeStudentReliability(StudentReliability $studentReliability): self
    {
        if ($this->studentReliability->contains($studentReliability)) {
            $this->studentReliability->removeElement($studentReliability);
            $studentReliability->removeIncident($this);
        }

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIncidentType(): ?string
    {
        return $this->incidentType;
    }

    public function setIncidentType(?string $incidentType): self
    {
        $this->incidentType = $incidentType;

        return $this;
    }
}
