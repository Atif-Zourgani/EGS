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
 * @ORM\Entity(repositoryClass="App\Repository\StudentRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class Student
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="student", cascade={"persist", "remove"})
     */
    private $user;

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
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="students_photos", fileNameProperty="image")
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $discord;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Section", inversedBy="students")
     */
    private $section;


    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\StudentParent", mappedBy="student")
     */
    private $studentParents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="student")
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $category;


    /**
     * @ORM\OneToMany(targetEntity="StudentReliability", mappedBy="student")
     */
    private $reliability;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $points;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentSkill", mappedBy="student")
     */
    private $studentSkills;

    /**
     * @ORM\OneToMany(targetEntity="StudentCall", mappedBy="student")
     */
    private $studentCalls;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $enabled;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $elite;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $challenger;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Feed", mappedBy="student")
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
            $this->category = 'student';
        }
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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


    public function __construct()
    {
        $this->studentParents = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->reliability = new ArrayCollection();
        $this->points = 20;
        $this->enabled = 1;
        $this->updatedAt = new \DateTime('Europe/Paris');
        $this->studentSkills = new ArrayCollection();
        $this->studentCalls = new ArrayCollection();
        $this->feeds = new ArrayCollection();
    }

    public function __toString()
    {
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getDiscord(): ?string
    {
        return $this->discord;
    }

    public function setDiscord(?string $discord): self
    {
        $this->discord = $discord;

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return Collection|StudentParent[]
     */
    public function getStudentParents(): Collection
    {
        return $this->studentParents;
    }

    public function addStudentParent(StudentParent $studentParent): self
    {
        if (!$this->studentParents->contains($studentParent)) {
            $this->studentParents[] = $studentParent;
            $studentParent->addStudent($this);
        }

        return $this;
    }

    public function removeStudentParent(StudentParent $studentParent): self
    {
        if ($this->studentParents->contains($studentParent)) {
            $this->studentParents->removeElement($studentParent);
            $studentParent->removeStudent($this);
        }

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
            $comment->setStudent($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getStudent() === $this) {
                $comment->setStudent(null);
            }
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
     * @return Collection|StudentReliability[]
     */
    public function getReliability(): Collection
    {
        return $this->reliability;
    }

    public function addReliability(StudentReliability $reliability): self
    {
        if (!$this->reliability->contains($reliability)) {
            $this->reliability[] = $reliability;
            $reliability->setStudent($this);
        }

        return $this;
    }

    public function removeReliability(StudentReliability $reliability): self
    {
        if ($this->reliability->contains($reliability)) {
            $this->reliability->removeElement($reliability);
            // set the owning side to null (unless already changed)
            if ($reliability->getStudent() === $this) {
                $reliability->setStudent(null);
            }
        }

        return $this;
    }


    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(?int $points): self
    {
        $this->points = $points;

        return $this;
    }

    /**
     * @return Collection|StudentSkill[]
     */
    public function getStudentSkills(): Collection
    {
        return $this->studentSkills;
    }

    public function addStudentSkill(StudentSkill $studentSkill): self
    {
        if (!$this->studentSkills->contains($studentSkill)) {
            $this->studentSkills[] = $studentSkill;
            $studentSkill->setStudent($this);
        }

        return $this;
    }

    public function removeStudentSkill(StudentSkill $studentSkill): self
    {
        if ($this->studentSkills->contains($studentSkill)) {
            $this->studentSkills->removeElement($studentSkill);
            // set the owning side to null (unless already changed)
            if ($studentSkill->getStudent() === $this) {
                $studentSkill->setStudent(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|StudentCall[]
     */
    public function getStudentCalls(): Collection
    {
        return $this->studentCalls;
    }

    public function addStudentCall(StudentCall $studentCall): self
    {
        if (!$this->studentCalls->contains($studentCall)) {
            $this->studentCalls[] = $studentCall;
            $studentCall->setStudent($this);
        }

        return $this;
    }

    public function removeStudentCall(StudentCall $studentCall): self
    {
        if ($this->studentCalls->contains($studentCall)) {
            $this->studentCalls->removeElement($studentCall);
            // set the owning side to null (unless already changed)
            if ($studentCall->getStudent() === $this) {
                $studentCall->setStudent(null);
            }
        }

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getElite(): ?bool
    {
        return $this->elite;
    }

    public function setElite(?bool $elite): self
    {
        $this->elite = $elite;

        return $this;
    }

    public function getChallenger(): ?bool
    {
        return $this->challenger;
    }

    public function setChallenger(?bool $challenger): self
    {
        $this->challenger = $challenger;

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
            $feed->setStudent($this);
        }

        return $this;
    }

    public function removeFeed(Feed $feed): self
    {
        if ($this->feeds->contains($feed)) {
            $this->feeds->removeElement($feed);
            // set the owning side to null (unless already changed)
            if ($feed->getStudent() === $this) {
                $feed->setStudent(null);
            }
        }

        return $this;
    }

}
