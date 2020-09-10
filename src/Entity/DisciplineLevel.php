<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DisciplineLevelRepository")
 */
class DisciplineLevel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Discipline", inversedBy="disciplineLevels")
     */
    private $discipline;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Level", inversedBy="disciplineLevels")
     */
    private $level;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Skill", mappedBy="disciplineLevel")
     */
    private $skills;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Exercise", mappedBy="disciplineLevel")
     */
    private $exercises;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\AcademicCareer", mappedBy="disciplineLevel")
     */
    private $academicCareers;

    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->exercises = new ArrayCollection();
        $this->academicCareers = new ArrayCollection();
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->getDiscipline()->getCategory() . " - " . $this->discipline . " - " . $this->level;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscipline(): ?Discipline
    {
        return $this->discipline;
    }

    public function setDiscipline(?Discipline $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection|Skill[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
            $skill->setDisciplineLevel($this);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        if ($this->skills->contains($skill)) {
            $this->skills->removeElement($skill);
            // set the owning side to null (unless already changed)
            if ($skill->getDisciplineLevel() === $this) {
                $skill->setDisciplineLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Exercise[]
     */
    public function getExercises(): Collection
    {
        return $this->exercises;
    }

    public function addExercise(Exercise $exercise): self
    {
        if (!$this->exercises->contains($exercise)) {
            $this->exercises[] = $exercise;
            $exercise->setDisciplineLevel($this);
        }

        return $this;
    }

    public function removeExercise(Exercise $exercise): self
    {
        if ($this->exercises->contains($exercise)) {
            $this->exercises->removeElement($exercise);
            // set the owning side to null (unless already changed)
            if ($exercise->getDisciplineLevel() === $this) {
                $exercise->setDisciplineLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AcademicCareer[]
     */
    public function getAcademicCareers(): Collection
    {
        return $this->academicCareers;
    }

    public function addAcademicCareer(AcademicCareer $academicCareer): self
    {
        if (!$this->academicCareers->contains($academicCareer)) {
            $this->academicCareers[] = $academicCareer;
            $academicCareer->addDisciplineLevel($this);
        }

        return $this;
    }

    public function removeAcademicCareer(AcademicCareer $academicCareer): self
    {
        if ($this->academicCareers->contains($academicCareer)) {
            $this->academicCareers->removeElement($academicCareer);
            $academicCareer->removeDisciplineLevel($this);
        }

        return $this;
    }
}
