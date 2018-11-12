<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UrlRepository")
 */
class Url
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=9)
     */
    private $short_stub;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $vanity;

    /**
     * @ORM\Column(type="integer")
     */
    private $redirect_count;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qr_code_address;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_on;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $long_url;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortStub(): ?string
    {
        return $this->short_stub;
    }

    public function setShortStub(string $short_stub): self
    {
        $this->short_stub = $short_stub;

        return $this;
    }

    public function getVanity(): ?string
    {
        return $this->vanity;
    }

    public function setVanity(?string $vanity): self
    {
        $this->vanity = $vanity;

        return $this;
    }

    public function getRedirectCount(): ?int
    {
        return $this->redirect_count;
    }

    public function setRedirectCount(int $redirect_count): self
    {
        $this->redirect_count = $redirect_count;

        return $this;
    }

    public function getQrCodeAddress(): ?string
    {
        return $this->qr_code_address;
    }

    public function setQrCodeAddress(?string $qr_code_address): self
    {
        $this->qr_code_address = $qr_code_address;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->created_on;
    }

    public function setCreatedOn(\DateTimeInterface $created_on): self
    {
        $this->created_on = $created_on;

        return $this;
    }

    public function getLongUrl(): ?string
    {
        return $this->long_url;
    }

    public function setLongUrl(string $long_url): self
    {
        $this->long_url = $long_url;

        return $this;
    }
}
