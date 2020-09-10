<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RollCallRepository")
 */
class RollCall
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="rollCalls")
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher", inversedBy="rollCalls")
     */
    private $teacher;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $halfDay;

    /**
     * @ORM\OneToMany(targetEntity="StudentCall", mappedBy="rollCall")
     */
    private $studentCalls;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Section", inversedBy="rollCalls")
     */
    private $section;

    public function __construct()
    {
        $this->studentCalls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

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

    public function getHalfDay(): ?string
    {
        return $this->halfDay;
    }

    public function setHalfDay(string $halfDay): self
    {
        $this->halfDay = $halfDay;

        return $this;
    }

    /**
     * @return Collection|StudentCall[]
     */
    public function getStudentCalls(): Collection
    {
        return $this->studentCalls;
    }

    public function addStudentCall(StudentCall $studentCall): self
    {
        if (!$this->studentCalls->contains($studentCall)) {
            $this->studentCalls[] = $studentCall;
            $studentCall->setRollCall($this);
        }

        return $this;
    }

    public function removeStudentCall(StudentCall $studentCall): self
    {
        if ($this->studentCalls->contains($studentCall)) {
            $this->studentCalls->removeElement($studentCall);
            // set the owning side to null (unless already changed)
            if ($studentCall->getRollCall() === $this) {
                $studentCall->setRollCall(null);
            }
        }

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }
}
