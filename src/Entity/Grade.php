<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GradeRepository")
 */
class Grade
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
     * @ORM\OneToMany(targetEntity="App\Entity\Section", mappedBy="grade")
     */
    private $sections;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AcademicCareer", mappedBy="grade")
     */
    private $academicCareers;

    public function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->academicCareers = new ArrayCollection();
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

    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): self
    {
        $this->shortname = $shortname;

        return $this;
    }

    /**
     * @return Collection|Section[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSections(Section $sections): self
    {
        if (!$this->sections->contains($sections)) {
            $this->sections[] = $sections;
            $sections->setGrade($this);
        }

        return $this;
    }

    public function removeSections(Section $sections): self
    {
        if ($this->sections->contains($sections)) {
            $this->sections->removeElement($sections);
            // set the owning side to null (unless already changed)
            if ($sections->getGrade() === $this) {
                $sections->setGrade(null);
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
            $academicCareer->setGrade($this);
        }

        return $this;
    }

    public function removeAcademicCareer(AcademicCareer $academicCareer): self
    {
        if ($this->academicCareers->contains($academicCareer)) {
            $this->academicCareers->removeElement($academicCareer);
            // set the owning side to null (unless already changed)
            if ($academicCareer->getGrade() === $this) {
                $academicCareer->setGrade(null);
            }
        }

        return $this;
    }

}
