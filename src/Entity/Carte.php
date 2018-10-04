<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarteRepository")
 */
class Carte
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
    private $type;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbCarte;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imgCarte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNbCarte(): ?int
    {
        return $this->nbCarte;
    }

    public function setNbCarte(int $nbCarte): self
    {
        $this->nbCarte = $nbCarte;

        return $this;
    }

    public function getImgCarte(): ?string
    {
        return $this->imgCarte;
    }

    public function setImgCarte(string $imgCarte): self
    {
        $this->imgCarte = $imgCarte;

        return $this;
    }
}
