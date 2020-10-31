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
    private $spend;

    public function __construct()
    {
        $this->setSpend(0);
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

    public function getSpend(): ?float
    {
        return $this->spend;
    }

    public function setSpend(float $spend): self
    {
        $this->spend = $spend;

        return $this;
    }
}
