<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Team
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
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastname;

    /**
     * @ORM\OneToMany(targetEntity="StudentReliability", mappedBy="team")
     */
    private $studentReliability;


    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->firstname . " " . $this->lastname;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullname;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RollCall", mappedBy="team")
     */
    private $rollCalls;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="team")
     */
    private $studentComments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Feed", mappedBy="team")
     */
    private $feeds;

    public function __construct()
    {
        $this->studentReliability = new ArrayCollection();
        $this->rollCalls = new ArrayCollection();
        $this->studentComments = new ArrayCollection();
        $this->feeds = new ArrayCollection();
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

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (empty($this->fullname)) {
            $this->fullname = $this->firstname . ' ' . $this->lastname;
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
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

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * @return Collection|StudentReliability[]
     */
    public function getStudentReliability(): Collection
    {
        return $this->studentReliability;
    }

    public function addStudentReliability(StudentReliability $studentReliability): self
    {
        if (!$this->studentReliability->contains($studentReliability)) {
            $this->studentReliability[] = $studentReliability;
            $studentReliability->setTeam($this);
        }

        return $this;
    }

    public function removeStudentReliability(StudentReliability $studentReliability): self
    {
        if ($this->studentReliability->contains($studentReliability)) {
            $this->studentReliability->removeElement($studentReliability);
            // set the owning side to null (unless already changed)
            if ($studentReliability->getTeam() === $this) {
                $studentReliability->setTeam(null);
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
            $rollCall->setTeam($this);
        }

        return $this;
    }

    public function removeRollCall(RollCall $rollCall): self
    {
        if ($this->rollCalls->contains($rollCall)) {
            $this->rollCalls->removeElement($rollCall);
            // set the owning side to null (unless already changed)
            if ($rollCall->getTeam() === $this) {
                $rollCall->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getStudentComments(): Collection
    {
        return $this->studentComments;
    }

    public function addStudentComment(Comment $studentComment): self
    {
        if (!$this->studentComments->contains($studentComment)) {
            $this->studentComments[] = $studentComment;
            $studentComment->setTeam($this);
        }

        return $this;
    }

    public function removeStudentComment(Comment $studentComment): self
    {
        if ($this->studentComments->contains($studentComment)) {
            $this->studentComments->removeElement($studentComment);
            // set the owning side to null (unless already changed)
            if ($studentComment->getTeam() === $this) {
                $studentComment->setTeam(null);
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
            $feed->setTeam($this);
        }

        return $this;
    }

    public function removeFeed(Feed $feed): self
    {
        if ($this->feeds->contains($feed)) {
            $this->feeds->removeElement($feed);
            // set the owning side to null (unless already changed)
            if ($feed->getTeam() === $this) {
                $feed->setTeam(null);
            }
        }

        return $this;
    }


}
