<?php

namespace App\Entity;

use App\Repository\MarcadorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\String\u;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=MarcadorRepository::class)
 * @UniqueEntity("nome")
 */
class Marcador
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $nome;

    /**
     * @Assert\Length(max=140)
     * @ORM\Column(type="string", length=140, nullable=true)
     */
    private $descricao;

    /**
     * @Assert\Regex("/^#[0-9A-Fa-f]{6}$/")
     * @ORM\Column(type="string", length=7, nullable=true)
     */
    private $cor_marcador;

    /**
     * @ORM\ManyToMany(targetEntity=Especie::class, mappedBy="marcador")
     */
    private $especies;

    public function __construct()
    {
        $this->especies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = (string) u($nome)->trim()->lower()->title(true);

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(?string $descricao): self
    {
        if (!empty($descricao)) {
            $this->descricao = (string) u($descricao)->trim()->lower()->title(true);
        } else {
            $this->descricao = null;
        }

        return $this;
    }

    public function getCorMarcador(): ?string
    {
        return $this->cor_marcador;
    }

    public function setCorMarcador(?string $cor_marcador): self
    {
        if (!empty($cor_marcador)) {
            $this->cor_marcador = (string) u($cor_marcador)->trim()->lower();
        } else {
            $this->cor_marcador = null;
        }
        
        return $this;
    }

    /**
     * @return Collection<int, Especie>
     */
    public function getEspecies(): Collection
    {
        return $this->especies;
    }

    public function addEspecy(Especie $especy): self
    {
        if (!$this->especies->contains($especy)) {
            $this->especies[] = $especy;
            $especy->addMarcador($this);
        }

        return $this;
    }

    public function removeEspecy(Especie $especy): self
    {
        if ($this->especies->removeElement($especy)) {
            $especy->removeMarcador($this);
        }

        return $this;
    }
}
