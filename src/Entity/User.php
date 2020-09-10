<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Teacher")
     */
    private $teacher;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Team")
     */
    private $team;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Student", mappedBy="user", cascade={"persist", "remove"})
     */
    private $student;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\StudentParent")
     */
    private $studentParent;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $passwordChangedAt;


    public function __construct()
    {
        parent::__construct();
        $this->plainPassword = 'Ut65vTIjbWca';
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }


    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $student === null ? null : $this;

        if ($newUser !== $student->getUser()) {
            //dd($student);
            $student->setUser($newUser);
            $this->setStudent($student);
        }

        return $this;
    }

    public function getStudentParent(): ?StudentParent
    {
        return $this->studentParent;
    }

    public function setStudentParent(?StudentParent $studentParent): self
    {
        $this->studentParent = $studentParent;

        return $this;
    }

    public function getPasswordChangedAt(): ?\DateTimeInterface
    {
        return $this->passwordChangedAt;
    }

    public function setPasswordChangedAt(?\DateTimeInterface $passwordChangedAt): self
    {
        $this->passwordChangedAt = $passwordChangedAt;

        return $this;
    }

}
