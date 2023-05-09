<?php

namespace App\Entity;

use Assert\NotBlank;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Reclamations
 *
 * @ORM\Table(name="reclamations")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ReclamationRepository")

 */

class Reclamations
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="etat", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Le nom ne peut pas être vide.")
     * @Assert\Length(max=50, maxMessage="Le nom ne peut pas contenir plus de {{ limit }} caractères.")
     * @Assert\Length(min=5, minMessage="Le nom ne peut pas contenir moin de {{ limit }} caractères.")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     message="Le nom ne doit pas contenir de chiffres ou de caractères spéciaux."
     * )
     */
    #[Assert\NotBlank(message:'le champs etat doit etre rempli')]
    #[Assert\Length(
        min: 10,
        max: 200,
        minMessage: 'message tres court ',
        maxMessage: 'message tres long',
    )]
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Le nom ne peut pas être vide.")
     * @Assert\Length(max=50, maxMessage="Le nom ne peut pas contenir plus de {{ limit }} caractères.")
     * @Assert\Length(min=5, minMessage="Le nom ne peut pas contenir moin de {{ limit }} caractères.")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]+$/",
     *     message="Le nom ne doit pas contenir de chiffres ou de caractères spéciaux."
     * )
     */
    private $description;

    /**
     * @var \App\Entity\Reclamations

     * @ORM\ManyToOne(targetEntity="Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id_user")
     * })
     */
    private ?Utilisateur $Utilisateur = null;
    private $Reclamation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function getUtilisateur(): ?Utilisateur
    {
        return $this->Utilisateur;
    }

    public function setUtilisateur(?Utilisateur $Utilisateur): self
    {
        $this->Utilisateur = $Utilisateur;

        return $this;
    }
    
}
