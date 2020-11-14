<?php

namespace App\Entity;

use App\Repository\GoalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GoalRepository::class)
 */
class Goal
{
    const ATTRIBUTES_GOAL = ['id', 'name', 'fill', 'total', 'economy' => ['id', 'price', 'budget' => ['id', 'year', 'month']]];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity=Economy::class, mappedBy="goal")
     */
    private $economy;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="goals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     */
    private $fill;

    public function __construct()
    {
        $this->economy = new ArrayCollection();
        $this->setFill(0);
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

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection|Economy[]
     */
    public function getEconomy(): Collection
    {
        return $this->economy;
    }

    public function addEconomy(Economy $economy): self
    {
        if (!$this->economy->contains($economy)) {
            $this->economy[] = $economy;
            $economy->setGoal($this);
        }

        return $this;
    }

    public function removeEconomy(Economy $economy): self
    {
        if ($this->economy->contains($economy)) {
            $this->economy->removeElement($economy);
            // set the owning side to null (unless already changed)
            if ($economy->getGoal() === $this) {
                $economy->setGoal(null);
            }
        }

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

    public function getFill(): ?float
    {
        return $this->fill;
    }

    public function setFill(float $fill): self
    {
        $this->fill = $fill;

        return $this;
    }
}
