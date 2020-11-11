<?php

namespace App\Entity;

use App\Repository\BudgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BudgetRepository::class)
 */
class Budget
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $year;

    /**
     * @ORM\Column(type="integer")
     */
    private $month;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="budgets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     */
    private $startSpend;

    /**
     * @ORM\OneToMany(targetEntity=Outgo::class, mappedBy="budget", orphanRemoval=true)
     */
    private $outgos;

    /**
     * @ORM\OneToMany(targetEntity=RegularSpend::class, fetch="EAGER", mappedBy="budget", orphanRemoval=true)
     */
    private $regularSpends;

    public function __construct()
    {
        $this->setStartSpend(0);
        $this->outgos = new ArrayCollection();
        $this->regularSpends = new ArrayCollection();
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

    public function getMonthString(){
        switch($this->month)
        {
            case 2:
                return 'Février';
                break;
            case 3:
                return 'Mars';
                break;
            case 4:
                return 'Avril';
                break;
            case 5:
                return 'Mai';
                break;
            case 6:
                return 'Juin';
                break;
            case 7:
                return 'Juillet';
                break;
            case 8:
                return 'Août';
                break;
            case 9:
                return 'Septembre';
                break;
            case 10:
                return 'Octobre';
                break;
            case 11:
                return 'Novembre';
                break;
            case 12:
                return 'Décembre';
                break;
            default:
                return 'Janvier';
                break;
        }
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    public function getStartSpend(): ?float
    {
        return $this->startSpend;
    }

    public function setStartSpend(float $startSpend): self
    {
        $this->startSpend = $startSpend;

        return $this;
    }

    /**
     * @return Collection|Outgo[]
     */
    public function getOutgos(): Collection
    {
        return $this->outgos;
    }

    public function addOutgo(Outgo $outgo): self
    {
        if (!$this->outgos->contains($outgo)) {
            $this->outgos[] = $outgo;
            $outgo->setBudget($this);
        }

        return $this;
    }

    public function removeOutgo(Outgo $outgo): self
    {
        if ($this->outgos->contains($outgo)) {
            $this->outgos->removeElement($outgo);
            // set the owning side to null (unless already changed)
            if ($outgo->getBudget() === $this) {
                $outgo->setBudget(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RegularSpend[]
     */
    public function getRegularSpends(): Collection
    {
        return $this->regularSpends;
    }

    public function addRegularSpend(RegularSpend $regularSpend): self
    {
        if (!$this->regularSpends->contains($regularSpend)) {
            $this->regularSpends[] = $regularSpend;
            $regularSpend->setBudget($this);
        }

        return $this;
    }

    public function removeRegularSpend(RegularSpend $regularSpend): self
    {
        if ($this->regularSpends->contains($regularSpend)) {
            $this->regularSpends->removeElement($regularSpend);
            // set the owning side to null (unless already changed)
            if ($regularSpend->getBudget() === $this) {
                $regularSpend->setBudget(null);
            }
        }

        return $this;
    }
}
