<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JetonRepository")
 */
class Jeton
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
    private $nbJeton;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imgJeton;

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

    public function getNbJeton(): ?int
    {
        return $this->nbJeton;
    }

    public function setNbJeton(int $nbJeton): self
    {
        $this->nbJeton = $nbJeton;

        return $this;
    }

    public function getImgJeton(): ?string
    {
        return $this->imgJeton;
    }

    public function setImgJeton(string $imgJeton): self
    {
        $this->imgJeton = $imgJeton;

        return $this;
    }
}
