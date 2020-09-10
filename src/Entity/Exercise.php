<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExerciseRepository")
 */
class Exercise
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $link;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DisciplineLevel", inversedBy="exercises")
     */
    private $disciplineLevel;


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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

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
