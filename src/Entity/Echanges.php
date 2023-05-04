<?php

namespace App\Entity;
use App\Repository\EchangesRepository;
use App\Entity\Produit;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Echanges
 *
 * @ORM\Table(name="echanges")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\EchangesRepository")
 */
class Echanges
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_echange", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEchange;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", length=20, nullable=false)
     */
    private $statut = 'en cours';

    /**
     * @var string
     *  * @Assert\NotBlank(message="commentaire doit etre non vide")
     * 
     * @ORM\Column(name="commentaire", type="string", length=50, nullable=false)
     */
    private $commentaire;

    /**
     * @var App\Entity\Produit|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="produit_echange", referencedColumnName="id_produit")
     * })
     */
    private $produitEchange;

    /**
     * @var App\Entity\Produit|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="produit_offert", referencedColumnName="id_produit")
     * })
     */
    private $produitOffert;

    public function getIdEchange(): ?int
    {
        return $this->idEchange;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getProduitEchange(): ?Produit
    {
        return $this->produitEchange;
    }

    public function setProduitEchange(?Produit $produitEchange): self
    {
        $this->produitEchange = $produitEchange;

        return $this;
    }

    public function getProduitOffert(): ?Produit
    {
        return $this->produitOffert;
    }

    public function setProduitOffert(?Produit $produitOffert): self
    {
        $this->produitOffert = $produitOffert;

        return $this;
    }

}
