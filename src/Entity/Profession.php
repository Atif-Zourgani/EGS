<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProfessionRepository")
 */
class Profession
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
     * @ORM\Column(type="string", length=255)
     */
    private $shortname;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\AcademicCareer", mappedBy="profession", cascade={"persist", "remove"})
     */
    private $academicCareer;


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

    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): self
    {
        $this->shortname = $shortname;

        return $this;
    }

    public function getAcademicCareer(): ?AcademicCareer
    {
        return $this->academicCareer;
    }

    public function setAcademicCareer(?AcademicCareer $academicCareer): self
    {
        $this->academicCareer = $academicCareer;

        // set (or unset) the owning side of the relation if necessary
        $newProfession = $academicCareer === null ? null : $this;
        if ($newProfession !== $academicCareer->getProfession()) {
            $academicCareer->setProfession($newProfession);
        }

        return $this;
    }
}
