<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PartieRepository")
 */
class Partie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="array")
     */
    private $Terrain = [];

    /**
     * @ORM\Column(type="array")
     */
    private $Deck = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $Defausse;

    /**
     * @ORM\Column(type="array")
     */
    private $tasJeton = [];

    /**
     * @ORM\Column(type="array")
     */
    private $HistoriquePartie = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $MainJoueur;

    /**
     * @ORM\Column(type="array")
     */
    private $Status = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Joueur", inversedBy="parties",cascade={"persist"}, fetch="EAGER")
     */
    private $Joueurs;

    public function __construct()
    {
        $this->Joueurs = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTerrain(): ?array
    {
        return $this->Terrain;
    }

    public function setTerrain(array $Terrain): self
    {
        $this->Terrain = $Terrain;

        return $this;
    }

    public function getDeck(): ?array
    {
        return $this->Deck;
    }

    public function setDeck(array $Deck): self
    {
        $this->Deck = $Deck;

        return $this;
    }

    public function getDefausse(): ?bool
    {
        return $this->Defausse;
    }

    public function setDefausse(bool $Defausse): self
    {
        $this->Defausse = $Defausse;

        return $this;
    }

    public function getTasJeton(): ?array
    {
        return $this->tasJeton;
    }

    public function setTasJeton(array $tasJeton): self
    {
        $this->tasJeton = $tasJeton;

        return $this;
    }

    public function getHistoriquePartie(): ?array
    {
        return $this->HistoriquePartie;
    }

    public function setHistoriquePartie(array $HistoriquePartie): self
    {
        $this->HistoriquePartie = $HistoriquePartie;

        return $this;
    }

    public function getMainJoueur(): ?bool
    {
        return $this->MainJoueur;
    }

    public function setMainJoueur(bool $MainJoueur): self
    {
        $this->MainJoueur = $MainJoueur;

        return $this;
    }

    public function getStatus(): ?array
    {
        return $this->Status;
    }

    public function setStatus(array $Status): self
    {
        $this->Status = $Status;

        return $this;
    }

    /**
     * @return Collection|Joueur[]
     */
    public function getJoueurs(): Collection
    {
        return $this->Joueurs;
    }

    public function addJoueur(Joueur $joueur): self
    {
        if (!$this->Joueurs->contains($joueur)) {
            $this->Joueurs[] = $joueur;
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): self
    {
        if ($this->Joueurs->contains($joueur)) {
            $this->Joueurs->removeElement($joueur);
        }

        return $this;
    }
}
