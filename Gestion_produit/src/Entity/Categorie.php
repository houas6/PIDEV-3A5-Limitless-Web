<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Categorie
 *
 * @ORM\Table(name="categorie")
 * @ORM\Entity(repositoryClass="App\Repository\CategorieRepository")
 *
 */
class Categorie
{
    

    /**
     * @var int
     *
     * @ORM\Column(name="idcategorie", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * *@Groups("post:read")
     */
    private $idcategorie;

    /**
     * @var string
     *  * @Assert\NotBlank(message=" nom categorie doit etre non vide")
     *@Assert\Length(
     *      min = 5,
     *      minMessage=" Entrer un nom au minimum de 5 caracteres"
     *
     *     )
     * @ORM\Column(name="nomcategorie", type="string", length=30, nullable=false)
     *  @Groups("post:read")
     */
    private $nomcategorie;

    

    public function getIdcategorie(): ?int
    {
        return $this->idcategorie;
    }

    public function setIdcategorie(int $idcategorie): self
    {
        $this->idcategorie = $idcategorie;

        return $this;
    }

    public function getNomcategorie(): ?string
    {
        return $this->nomcategorie;
    }

    public function setNomcategorie(string $nomcategorie): self
    {
        $this->nomcategorie = $nomcategorie;

        return $this;
    }
    public function __toString()
    {
        return $this->nomcategorie;
    }
}
