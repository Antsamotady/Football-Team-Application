<?php

namespace App\Entity;

use App\Repository\JournalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalRepository::class)]
class Journal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $Cleabo;

    #[ORM\Column(type: 'datetime')]
    private $horodate;

    #[ORM\Column(type: 'string', length: 255)]
    private $IPconnect;

    #[ORM\Column(type: 'string', length: 255)]
    private $Statut;

    #[ORM\Column(type: 'integer')]
    private $rang;

    #[ORM\Column(type: 'string', length: 255)]
    private $login;

    #[ORM\Column(type: 'string', length: 255)]
    private $Hash;

    #[ORM\Column(type: 'string', length: 255)]
    private $SynthAbo;

    #[ORM\Column(type: 'string', length: 255)]
    private $UserAgent;

    #[ORM\Column(type: 'string', length: 255)]
    private $HtmlHeader;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $sessionId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCleabo(): ?string
    {
        return $this->Cleabo;
    }

    public function setCleabo(string $Cleabo): self
    {
        $this->Cleabo = $Cleabo;

        return $this;
    }

    public function getHorodate(): ?\DateTimeInterface
    {
        return $this->horodate;
    }

    public function setHorodate(\DateTimeInterface $horodate): self
    {
        $this->horodate = $horodate;

        return $this;
    }

    public function getIPconnect(): ?string
    {
        return $this->IPconnect;
    }

    public function setIPconnect(string $IPconnect): self
    {
        $this->IPconnect = $IPconnect;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->Statut;
    }

    public function setStatut(string $Statut): self
    {
        $this->Statut = $Statut;

        return $this;
    }

    public function getRang(): ?int
    {
        return $this->rang;
    }

    public function setRang(int $rang): self
    {
        $this->rang = $rang;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->Hash;
    }

    public function setHash(string $Hash): self
    {
        $this->Hash = $Hash;

        return $this;
    }

    public function getSynthAbo(): ?string
    {
        return $this->SynthAbo;
    }

    public function setSynthAbo(string $SynthAbo): self
    {
        $this->SynthAbo = $SynthAbo;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->UserAgent;
    }

    public function setUserAgent(string $UserAgent): self
    {
        $this->UserAgent = $UserAgent;

        return $this;
    }

    public function getHtmlHeader(): ?string
    {
        return $this->HtmlHeader;
    }

    public function setHtmlHeader(string $HtmlHeader): self
    {
        $this->HtmlHeader = $HtmlHeader;

        return $this;
    }

    /**
     * Get the value of sessionId
     */ 
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set the value of sessionId
     *
     * @return  self
     */ 
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }
}
