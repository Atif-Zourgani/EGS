<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LevelRepository")
 */
class Level
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
     * @ORM\OneToMany(targetEntity="App\Entity\DisciplineLevel", mappedBy="level")
     */
    private $disciplineLevels;


    public function __construct()
    {
        $this->disciplineLevels = new ArrayCollection();
    }

    public function __toString()
    {
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
     * @return Collection|DisciplineLevel[]
     */
    public function getDisciplineLevels(): Collection
    {
        return $this->disciplineLevels;
    }

    public function addDisciplineLevel(DisciplineLevel $disciplineLevel): self
    {
        if (!$this->disciplineLevels->contains($disciplineLevel)) {
            $this->disciplineLevels[] = $disciplineLevel;
            $disciplineLevel->setLevel($this);
        }

        return $this;
    }

    public function removeDisciplineLevel(DisciplineLevel $disciplineLevel): self
    {
        if ($this->disciplineLevels->contains($disciplineLevel)) {
            $this->disciplineLevels->removeElement($disciplineLevel);
            // set the owning side to null (unless already changed)
            if ($disciplineLevel->getLevel() === $this) {
                $disciplineLevel->setLevel(null);
            }
        }

        return $this;
    }
}
