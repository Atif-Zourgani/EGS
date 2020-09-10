<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\TeacherRepository")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class Teacher
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
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="teachers_photos", fileNameProperty="image")
     * @Assert\File(maxSize= "1000k", maxSizeMessage= "Le fichier ne doit pas excéder 1000k.")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="teacher")
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Discipline", inversedBy="teachers")
     */
    private $discipline;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentSkill", mappedBy="teacher")
     */
    private $evaluations;

    /**
     * @ORM\OneToMany(targetEntity="StudentReliability", mappedBy="teacher")
     */
    private $studentReliability;


    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->discipline = new ArrayCollection();
        $this->evaluations = new ArrayCollection();
        $this->studentReliability = new ArrayCollection();
        $this->rollCalls = new ArrayCollection();
        $this->feeds = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RollCall", mappedBy="teacher")
     */
    private $rollCalls;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Feed", mappedBy="teacher")
     */
    private $feeds;


    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (empty($this->fullname)) {
            $this->fullname = $this->firstname . ' ' . $this->lastname;
        }

        if (empty($this->category)) {
            $this->category = 'teacher';
        }
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->firstname . " " . $this->lastname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param mixed $fullname
     */
    public function setFullname($fullname): void
    {
        $this->fullname = $fullname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getImageFile(): ?File
    {
        return $this->imageFile;
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
            $comment->setTeacher($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getTeacher() === $this) {
                $comment->setTeacher(null);
            }
        }

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection|Discipline[]
     */
    public function getDiscipline(): Collection
    {
        return $this->discipline;
    }

    public function addDiscipline(Discipline $discipline): self
    {
        if (!$this->discipline->contains($discipline)) {
            $this->discipline[] = $discipline;
        }

        return $this;
    }

    public function removeDiscipline(Discipline $discipline): self
    {
        if ($this->discipline->contains($discipline)) {
            $this->discipline->removeElement($discipline);
        }

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|StudentSkill[]
     */
    public function getEvaluations(): Collection
    {
        return $this->evaluations;
    }

    public function addEvaluation(StudentSkill $evaluation): self
    {
        if (!$this->evaluations->contains($evaluation)) {
            $this->evaluations[] = $evaluation;
            $evaluation->setTeacher($this);
        }

        return $this;
    }

    public function removeEvaluation(StudentSkill $evaluation): self
    {
        if ($this->evaluations->contains($evaluation)) {
            $this->evaluations->removeElement($evaluation);
            // set the owning side to null (unless already changed)
            if ($evaluation->getTeacher() === $this) {
                $evaluation->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StudentReliability[]
     */
    public function getstudentReliability(): Collection
    {
        return $this->studentReliability;
    }

    public function addstudentReliability(StudentReliability $studentReliability): self
    {
        if (!$this->studentReliability->contains($studentReliability)) {
            $this->studentReliability[] = $studentReliability;
            $studentReliability->setTeacher($this);
        }

        return $this;
    }

    public function removestudentReliability(StudentReliability $studentReliability): self
    {
        if ($this->studentReliability->contains($studentReliability)) {
            $this->studentReliability->removeElement($studentReliability);
            // set the owning side to null (unless already changed)
            if ($studentReliability->getTeacher() === $this) {
                $studentReliability->setTeacher(null);
            }
        }

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
            $rollCall->setTeacher($this);
        }

        return $this;
    }

    public function removeRollCall(RollCall $rollCall): self
    {
        if ($this->rollCalls->contains($rollCall)) {
            $this->rollCalls->removeElement($rollCall);
            // set the owning side to null (unless already changed)
            if ($rollCall->getTeacher() === $this) {
                $rollCall->setTeacher(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Feed[]
     */
    public function getFeeds(): Collection
    {
        return $this->feeds;
    }

    public function addFeed(Feed $feed): self
    {
        if (!$this->feeds->contains($feed)) {
            $this->feeds[] = $feed;
            $feed->setTeacher($this);
        }

        return $this;
    }

    public function removeFeed(Feed $feed): self
    {
        if ($this->feeds->contains($feed)) {
            $this->feeds->removeElement($feed);
            // set the owning side to null (unless already changed)
            if ($feed->getTeacher() === $this) {
                $feed->setTeacher(null);
            }
        }

        return $this;
    }

}
