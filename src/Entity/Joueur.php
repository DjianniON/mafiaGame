<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\JoueurRepository")
 */
class Joueur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="joueurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Users;

    /**
     * @ORM\Column(type="array")
     */
    private $Main = [];

    /**
     * @ORM\Column(type="array")
     */
    private $tasJetons = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $score;

    /**
     * @ORM\Column(type="array")
     */
    private $Chameaux = [];

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Partie", mappedBy="Joueurs",cascade={"persist"})
     */
    private $parties;

    public function __construct()
    {
        $this->parties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsers(): ?User
    {
        return $this->Users;
    }

    public function setUsers(?User $Users): self
    {
        $this->Users = $Users;

        return $this;
    }



    public function getMain(): ?array
    {
        return $this->Main;
    }

    public function setMain(array $Main): self
    {
        $this->Main = $Main;

        return $this;
    }

    public function getTasJetons(): ?array
    {
        return $this->tasJetons;
    }

    public function setTasJetons(array $tasJetons): self
    {
        $this->tasJetons = $tasJetons;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getChameaux(): ?array
    {
        return $this->Chameaux;
    }

    public function setChameaux(array $Chameaux): self
    {
        $this->Chameaux = $Chameaux;

        return $this;
    }

    /**
     * @return Collection|Partie[]
     */
    public function getParties(): Collection
    {
        return $this->parties;
    }

    public function addParty(Partie $party): self
    {
        if (!$this->parties->contains($party)) {
            $this->parties[] = $party;
            $party->addJoueur($this);
        }

        return $this;
    }

    public function removeParty(Partie $party): self
    {
        if ($this->parties->contains($party)) {
            $this->parties->removeElement($party);
            $party->removeJoueur($this);
        }

        return $this;
    }
}
