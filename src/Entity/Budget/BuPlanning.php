<?php

namespace App\Entity\Budget;

use App\Entity\User;
use App\Repository\Budget\BuPlanningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BuPlanningRepository::class)
 */
class BuPlanning
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $year;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private $month;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="plannings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     * @Groups({"user:read"})
     */
    private $start;

    /**
     * @ORM\Column(type="float")
     * @Groups({"user:read"})
     */
    private $end;

    /**
     * @ORM\OneToMany(targetEntity=BuExpense::class, fetch="EAGER", mappedBy="planning", orphanRemoval=true)
     * @Groups({"user:read"})
     */
    private $expenses;

    public function __construct()
    {
        $this->start = 0;
        $this->end = 0;
        $this->expenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
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

    public function getStart(): ?float
    {
        return $this->start;
    }

    public function setStart(float $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?float
    {
        return $this->end;
    }

    public function setEnd(float $end): self
    {
        $this->end = $end;

        return $this;
    }

    /**
     * @return Collection|BuExpense[]
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(BuExpense $expense): self
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses[] = $expense;
            $expense->setPlanning($this);
        }

        return $this;
    }

    public function removeExpense(BuExpense $expense): self
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getPlanning() === $this) {
                $expense->setPlanning(null);
            }
        }

        return $this;
    }
}
