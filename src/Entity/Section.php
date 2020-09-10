<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Section
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
     * @ORM\OneToMany(targetEntity="App\Entity\Student", mappedBy="section")
     */
    private $students;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Grade", inversedBy="sections")
     */
    private $grade;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RollCall", mappedBy="section")
     */
    private $rollCalls;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="sections_photos", fileNameProperty="image")
     * @Assert\File(maxSize= "1000k", maxSizeMessage= "Le fichier ne doit pas excéder 1000ko.")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pathway", inversedBy="sections")
     */
    private $pathway;


    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->rollCalls = new ArrayCollection();
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
     * @return Collection|Student[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setSection($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
            // set the owning side to null (unless already changed)
            if ($student->getSection() === $this) {
                $student->setSection(null);
            }
        }

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
     * @return Collection|RollCall[]
     */
    public function getRollCalls(): Collection
    {
        return $this->rollCalls;
    }

    public function addRollCall(RollCall $rollCall): self
    {
        if (!$this->rollCalls->contains($rollCall)) {
            $this->rollCalls[] = $rollCall;
            $rollCall->setSection($this);
        }

        return $this;
    }

    public function removeRollCall(RollCall $rollCall): self
    {
        if ($this->rollCalls->contains($rollCall)) {
            $this->rollCalls->removeElement($rollCall);
            // set the owning side to null (unless already changed)
            if ($rollCall->getSection() === $this) {
                $rollCall->setSection(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @param File|UploadedFile $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime('now');

        return $this;
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
}
