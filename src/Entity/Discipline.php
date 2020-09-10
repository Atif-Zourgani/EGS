<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DisciplineRepository")
 * @Vich\Uploadable
 */
class Discipline
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="discipline")
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DisciplineCat", inversedBy="disciplines")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="disciplines_logos", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Teacher", mappedBy="discipline")
     */
    private $teachers;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DisciplineLevel", mappedBy="discipline")
     */
    private $disciplineLevels;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->teachers = new ArrayCollection();
        $this->disciplineLevels = new ArrayCollection();
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
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setDiscipline($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getDiscipline() === $this) {
                $comment->setDiscipline(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?DisciplineCat
    {
        return $this->category;
    }

    public function setCategory(?DisciplineCat $category): self
    {
        $this->category = $category;

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
    public function getUpdatedAt(): \DateTime
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

    /**
     * @return Collection|Teacher[]
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): self
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers[] = $teacher;
            $teacher->addDiscipline($this);
        }

        return $this;
    }

    public function removeTeacher(Teacher $teacher): self
    {
        if ($this->teachers->contains($teacher)) {
            $this->teachers->removeElement($teacher);
            $teacher->removeDiscipline($this);
        }

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
            $disciplineLevel->setDiscipline($this);
        }

        return $this;
    }

    public function removeDisciplineLevel(DisciplineLevel $disciplineLevel): self
    {
        if ($this->disciplineLevels->contains($disciplineLevel)) {
            $this->disciplineLevels->removeElement($disciplineLevel);
            // set the owning side to null (unless already changed)
            if ($disciplineLevel->getDiscipline() === $this) {
                $disciplineLevel->setDiscipline(null);
            }
        }

        return $this;
    }

}
