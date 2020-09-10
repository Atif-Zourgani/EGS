<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AcademicCareerRepository")
 */
class AcademicCareer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pathway", inversedBy="academicCareers")
     */
    private $pathway;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Grade", inversedBy="academicCareers")
     */
    private $grade;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\DisciplineLevel", inversedBy="academicCareers")
     */
    private $disciplineLevel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PathwaySpecialism", inversedBy="academicCareers")
     */
    private $specialism;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Profession", inversedBy="academicCareer", cascade={"persist", "remove"})
     */
    private $profession;

    public function __construct()
    {
        $this->disciplineLevel = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPathway(): ?Pathway
    {
        return $this->pathway;
    }

    public function setPathway(?Pathway $pathway): self
    {
        $this->pathway = $pathway;

        return $this;
    }

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * @return Collection|DisciplineLevel[]
     */
    public function getDisciplineLevel(): Collection
    {
        return $this->disciplineLevel;
    }

    public function addDisciplineLevel(DisciplineLevel $disciplineLevel): self
    {
        if (!$this->disciplineLevel->contains($disciplineLevel)) {
            $this->disciplineLevel[] = $disciplineLevel;
        }

        return $this;
    }

    public function removeDisciplineLevel(DisciplineLevel $disciplineLevel): self
    {
        if ($this->disciplineLevel->contains($disciplineLevel)) {
            $this->disciplineLevel->removeElement($disciplineLevel);
        }

        return $this;
    }

    public function getSpecialism(): ?PathwaySpecialism
    {
        return $this->specialism;
    }

    public function setSpecialism(?PathwaySpecialism $specialism): self
    {
        $this->specialism = $specialism;

        return $this;
    }

    public function getProfession(): ?Profession
    {
        return $this->profession;
    }

    public function setProfession(?Profession $profession): self
    {
        $this->profession = $profession;

        return $this;
    }
}
