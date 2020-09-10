<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PathwayRepository")
 */
class Pathway
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
     * @ORM\OneToMany(targetEntity="App\Entity\Section", mappedBy="pathway")
     */
    private $sections;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AcademicCareer", mappedBy="pathway")
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

    /**
     * @return Collection|Section[]
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): self
    {
        if (!$this->sections->contains($section)) {
            $this->sections[] = $section;
            $section->setPathway($this);
        }

        return $this;
    }

    public function removeSection(Section $section): self
    {
        if ($this->sections->contains($section)) {
            $this->sections->removeElement($section);
            // set the owning side to null (unless already changed)
            if ($section->getPathway() === $this) {
                $section->setPathway(null);
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
            $academicCareer->setPathway($this);
        }

        return $this;
    }

    public function removeAcademicCareer(AcademicCareer $academicCareer): self
    {
        if ($this->academicCareers->contains($academicCareer)) {
            $this->academicCareers->removeElement($academicCareer);
            // set the owning side to null (unless already changed)
            if ($academicCareer->getPathway() === $this) {
                $academicCareer->setPathway(null);
            }
        }

        return $this;
    }
}
