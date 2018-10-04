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
     * @ORM\ManyToMany(targetEntity="App\Entity\Partie", inversedBy="joueurs")
     */
    private $Parties;

    public function __construct()
    {
        $this->Parties = new ArrayCollection();
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

    /**
     * @return Collection|Partie[]
     */
    public function getParties(): Collection
    {
        return $this->Parties;
    }

    public function addParty(Partie $party): self
    {
        if (!$this->Parties->contains($party)) {
            $this->Parties[] = $party;
        }

        return $this;
    }

    public function removeParty(Partie $party): self
    {
        if ($this->Parties->contains($party)) {
            $this->Parties->removeElement($party);
        }

        return $this;
    }
}
