<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentReliabilityRepository")
 */
class StudentReliability
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="reliability")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="studentReliability")
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="studentReliability")
     */
    private $team;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Incident", inversedBy="studentReliability")
     * @ORM\JoinColumn(nullable=true)
     */
    private $incident;


    public function __construct($student, $teacher, $team)
    {
        $this->incident = new ArrayCollection();
        $this->createdAt = new \DateTime('Europe/Paris');
        $this->student = $student;
        $this->teacher = $teacher;
        $this->team = $team;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->student;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime('now');

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection|Incident[]
     */
    public function getIncident(): Collection
    {
        return $this->incident;
    }

    public function addIncident(Incident $incident): self
    {
        if (!$this->incident->contains($incident)) {
            $this->incident[] = $incident;
        }

        return $this;
    }

    public function removeIncident(Incident $incident): self
    {
        if ($this->incident->contains($incident)) {
            $this->incident->removeElement($incident);
        }

        return $this;
    }

}
