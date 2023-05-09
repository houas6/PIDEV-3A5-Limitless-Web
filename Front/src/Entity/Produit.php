<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * Produit
 *
 * @ORM\Table(name="produit")
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_produit", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *@Groups("produits:read")
     */
    private $id_produit;

    /**
     * @var string
     *  * @Assert\NotBlank(message=" nom doit etre non vide")
     * @Assert\Length(
     *      min = 4,
     *      minMessage=" Entrer un nom au minimum de 4 caracteres"
     *
     *     )
     * @ORM\Column(name="nom_produit", type="string", length=30, nullable=false)
     *@Groups("produits:read")
     */
    private $nomproduit;

    /**
     * @var float
     ** @Assert\NotBlank(message=" prix doit etre non vide")
     * @Assert\Positive
     * @ORM\Column(name="prix", type="float", precision=10, scale=0, nullable=false)
     *@Groups("produits:read")
     */
    private $prix;

    /**
     * @var string
      * @Assert\NotBlank(message=" description doit etre non vide")
     * @Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer ue description au minimum de 5 caracteres"
     *
     *     )
     * @ORM\Column(name="description", type="string", length=30, nullable=false)
     *@Groups("produits:read")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=30, nullable=false)
     *@Groups("produits:read")
     */
    private $image;

    /**
     * @var \App\Entity\Utilisateur|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Utilisateur")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     * })
     *@Groups("produits:read")
     */
    private $idUser;

    /**
     * @var \App\Entity\Categorie|null
     * 
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idcategorie", referencedColumnName="idcategorie")
     *  })
     * *@Groups("produits:read")
     */
    private $idcategorie;


    

    public function getIdproduit(): ?int
    {
        return $this->id_produit;
    }

    public function getId_produit(): ?int
    {
        return $this->id_produit;
    }

    public function setId_produit(int $id_produit): self
    {
        $this->id_produit = $id_produit;

        return $this;
    }

    public function getNomProduit(): ?string
    {
        return $this->nomproduit;
    }

    public function setNomProduit(string $nomproduit): self
    {
        $this->nomproduit = $nomproduit;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

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

    public function getIdUser(): ?Utilisateur
    {
        return $this->idUser;
    }

    public function setIdUser(?Utilisateur $utilisateur): self
    {
        $this->idUser = $utilisateur;

        return $this;
    }
    public function getIdcategorie(): ?Categorie
    {
        return $this->idcategorie;
    }

    public function setIdcategorie(?Categorie $categorie): self
    {
        $this->idcategorie = $categorie;

        return $this;
    }



    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    

}
