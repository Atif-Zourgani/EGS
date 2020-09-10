<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SkillRepository")
 * @UniqueEntity("description")
 */
class Skill
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\StudentSkill", mappedBy="skills")
     */
    private $studentSkills;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DisciplineLevel", inversedBy="skills")
     */
    private $disciplineLevel;

    public function __construct()
    {
        $this->studentSkills = new ArrayCollection();
    }


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->description;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }


    /**
     * @return Collection|StudentSkill[]
     */
    public function getStudentSkills(): Collection
    {
        return $this->studentSkills;
    }

    public function addStudentSkill(StudentSkill $studentSkill): self
    {
        if (!$this->studentSkills->contains($studentSkill)) {
            $this->studentSkills[] = $studentSkill;
            $studentSkill->addSkill($this);
        }

        return $this;
    }

    public function removeStudentSkill(StudentSkill $studentSkill): self
    {
        if ($this->studentSkills->contains($studentSkill)) {
            $this->studentSkills->removeElement($studentSkill);
            $studentSkill->removeSkill($this);
        }

        return $this;
    }


    public function getDisciplineLevel(): ?DisciplineLevel
    {
        return $this->disciplineLevel;
    }

    public function setDisciplineLevel(?DisciplineLevel $disciplineLevel): self
    {
        $this->disciplineLevel = $disciplineLevel;

        return $this;
    }

}
