<?php

namespace App\Entity\Budget;

use App\Entity\DataEntity;
use App\Repository\Budget\BuExpenseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=BuExpenseRepository::class)
 */
class BuExpense extends DataEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read", "planning-item:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read", "planning-item:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     * @Groups({"user:read", "planning-item:read"})
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"user:read", "planning-item:read"})
     */
    private $icon;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"user:read", "planning-item:read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=BuPlanning::class, fetch="EAGER", inversedBy="expenses")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"planning-item:read"})
     */
    private $planning;

    public function __construct()
    {
        $this->createdAt = $this->initNewDate();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * return ll -> 5 janv. 2017
     * return LL -> 5 janvier 2017
     *
     * @return string|null
     * @Groups({"user:read", "planning-item:read"})
     */
    public function getCreatedAtString(): ?string
    {
        return $this->getFullDateString($this->createdAt);
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPlanning(): ?BuPlanning
    {
        return $this->planning;
    }

    public function setPlanning(?BuPlanning $planning): self
    {
        $this->planning = $planning;

        return $this;
    }
}
