<?php

namespace App\Entity;
use App\Repository\LivraisonRepository;
use App\Entity\Livreur;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Livraison
 *
 * @ORM\Table(name="livraison")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\LivraisonRepository")
 */
class Livraison
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_livraison", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idLivraison;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_livraison", type="date", nullable=false)
     */
    private $dateLivraison;

    /**
     * @var string
     ** @Assert\NotBlank(message=" adresse livraison doit etre non vide")
     * @ORM\Column(name="adresse_livraison", type="string", length=20, nullable=false)
     */
    private $adresseLivraison;

 /**
 * @var int
 *
 * @Assert\NotBlank(message="Code postal doit être non vide")
 * @Assert\Length(
 *     min = 5,
 *     max = 5,
 *     exactMessage = "Code postal doit contenir exactement 5 chiffres"
 * )
 *
 * @ORM\Column(name="code_postal_livraison", type="integer", nullable=false)
 */
private $codePostalLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="status_livraison", type="string", length=20, nullable=false)
     */
    private $statusLivraison = 'en cours';

    /**
     * @var App\Entity\Livreur|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Livreur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ID_livreur", referencedColumnName="ID_livreur")
     * })
     */
    private $idLivreur;

    public function getIdLivraison(): ?int
    {
        return $this->idLivraison;
    }

    public function getDateLivraison(): ?\DateTimeInterface
    {
        return $this->dateLivraison;
    }

    public function setDateLivraison(\DateTimeInterface $dateLivraison): self
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    public function getAdresseLivraison(): ?string
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(string $adresseLivraison): self
    {
        $this->adresseLivraison = $adresseLivraison;

        return $this;
    }

    public function getCodePostalLivraison(): ?int
    {
        return $this->codePostalLivraison;
    }

    public function setCodePostalLivraison(int $codePostalLivraison): self
    {
        $this->codePostalLivraison = $codePostalLivraison;

        return $this;
    }

    public function getStatusLivraison(): ?string
    {
        return $this->statusLivraison;
    }

    public function setStatusLivraison(string $statusLivraison): self
    {
        $this->statusLivraison = $statusLivraison;

        return $this;
    }

    public function getIDLivreur(): ?Livreur
    {
        return $this->idLivreur;
    }

    public function setIDLivreur(?Livreur $idLivreur): self
    {
        $this->idLivreur = $idLivreur;

        return $this;
    }


}
