<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypeRepository")
 */
class Type
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
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Carte", mappedBy="type")
     */
    private $Cartes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Jeton", mappedBy="type")
     */
    private $Jetons;

    public function __construct()
    {
        $this->Cartes = new ArrayCollection();
        $this->Jetons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Carte[]
     */
    public function getCartes(): Collection
    {
        return $this->Cartes;
    }

    public function addCarte(Carte $carte): self
    {
        if (!$this->Cartes->contains($carte)) {
            $this->Cartes[] = $carte;
            $carte->setType($this);
        }

        return $this;
    }

    public function removeCarte(Carte $carte): self
    {
        if ($this->Cartes->contains($carte)) {
            $this->Cartes->removeElement($carte);
            // set the owning side to null (unless already changed)
            if ($carte->getType() === $this) {
                $carte->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Jeton[]
     */
    public function getJetons(): Collection
    {
        return $this->Jetons;
    }

    public function addJeton(Jeton $jeton): self
    {
        if (!$this->Jetons->contains($jeton)) {
            $this->Jetons[] = $jeton;
            $jeton->setType($this);
        }

        return $this;
    }

    public function removeJeton(Jeton $jeton): self
    {
        if ($this->Jetons->contains($jeton)) {
            $this->Jetons->removeElement($jeton);
            // set the owning side to null (unless already changed)
            if ($jeton->getType() === $this) {
                $jeton->setType(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return $this->nom;
    }
}
