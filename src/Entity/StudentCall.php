<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentCallRepository")
 */
class StudentCall
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="RollCall", inversedBy="studentCalls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rollCall;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="studentCalls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $student;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRollCall(): ?RollCall
    {
        return $this->rollCall;
    }

    public function setRollCall(?RollCall $rollCall): self
    {
        $this->rollCall = $rollCall;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
