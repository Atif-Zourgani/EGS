<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PathwaySpecialismRepository")
 */
class PathwaySpecialism
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
     * @ORM\OneToMany(targetEntity="App\Entity\AcademicCareer", mappedBy="specialism")
     */
    private $academicCareers;

    public function __construct()
    {
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
            $academicCareer->setSpecialism($this);
        }

        return $this;
    }

    public function removeAcademicCareer(AcademicCareer $academicCareer): self
    {
        if ($this->academicCareers->contains($academicCareer)) {
            $this->academicCareers->removeElement($academicCareer);
            // set the owning side to null (unless already changed)
            if ($academicCareer->getSpecialism() === $this) {
                $academicCareer->setSpecialism(null);
            }
        }

        return $this;
    }
}
