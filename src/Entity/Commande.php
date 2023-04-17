<?php

namespace App\Entity;

use Assert\NotBlank;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Commande
 *
 * @ORM\Table(name="commande", indexes={@ORM\Index(name="id_user", columns={"id_user"})})
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_commande", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCommande;

   /**
 * @var string
 *
 * @ORM\Column(name="nom", type="string", length=50, nullable=false)
 * @Assert\NotBlank(message="Le nom ne peut pas être vide.")
 * @Assert\Length(max=50, maxMessage="Le nom ne peut pas contenir plus de {{ limit }} caractères.")
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z]+$/",
 *     message="Le nom ne doit pas contenir de chiffres ou de caractères spéciaux."
 * )
 */
private $nom;

/**
 * @var string
 *
 * @ORM\Column(name="prenom", type="string", length=50, nullable=false)
 * @Assert\NotBlank(message="Le prénom ne peut pas être vide.")
 * @Assert\Length(max=50, maxMessage="Le prénom ne peut pas contenir plus de {{ limit }} caractères.")
 * @Assert\Regex(
 *     pattern="/^[a-zA-Z]+$/",
 *     message="Le prénom ne doit pas contenir de chiffres ou de caractères spéciaux."
 * )
 */
private $prenom;
   /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="L'adresse ne peut pas être vide.")
     * @Assert\Length(max=50, maxMessage="L'adresse ne peut pas contenir plus de {{ limit }} caractères.")
     */
    private $adresse;

    /**
 * @var float
 *
 * @ORM\Column(name="total", type="float", precision=10, scale=0, nullable=false)
 */
private $total;


    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=30, nullable=true, options={"default"="Nonpaye"})
     */
    private $status = 'Nonpaye';

    /**
     * @var \App\Entity\Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     * })
     */
    private $idUser;

    public function getIdCommande(): ?int
    {
        return $this->idCommande;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getIdUser(): ?Utilisateur
    {
        return $this->idUser;
    }

    public function setIdUser(?Utilisateur $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }


}
