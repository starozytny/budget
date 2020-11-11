<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingsRepository::class)
 */
class Settings
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
    private $email_contact;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_rgpd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email_global;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $website_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="text")
     */
    private $logo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxYear;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailContact(): ?string
    {
        return $this->email_contact;
    }

    public function setEmailContact(string $email_contact): self
    {
        $this->email_contact = $email_contact;

        return $this;
    }

    public function getEmailRgpd(): ?string
    {
        return $this->email_rgpd;
    }

    public function setEmailRgpd(string $email_rgpd): self
    {
        $this->email_rgpd = $email_rgpd;

        return $this;
    }

    public function getEmailGlobal(): ?string
    {
        return $this->email_global;
    }

    public function setEmailGlobal(string $email_global): self
    {
        $this->email_global = $email_global;

        return $this;
    }

    public function getWebsiteName(): ?string
    {
        return $this->website_name;
    }

    public function setWebsiteName(string $website_name): self
    {
        $this->website_name = $website_name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getMaxYear(): ?int
    {
        return $this->maxYear;
    }

    public function setMaxYear(?int $maxYear): self
    {
        $this->maxYear = $maxYear;

        return $this;
    }
}
