<?php

namespace App\Entity;

use Assert\NotBlank;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ReponseReclamation
 *
 * @ORM\Table(name="reponse_reclamation")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ReponseReclamationRepository")

 */
class ReponseReclamation
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
     *
     * @ORM\Column(name="contenu", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Le nom ne peut pas être vide.")
     * @Assert\Length(max=50, maxMessage="Le nom ne peut pas contenir plus de {{ limit }} caractères.")
     * @Assert\Length(min=5, minMessage="Le nom ne peut pas contenir moin de {{ limit }} caractères.")
     */
    private $contenu;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }
    public function __toString()
    {
        return $this->id;
    }
}
