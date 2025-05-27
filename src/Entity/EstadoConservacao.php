<?php

namespace App\Entity;

use App\Repository\EstadoConservacaoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\String\u;
use \DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EstadoConservacaoRepository::class)
 * @UniqueEntity("nome")
 */
class EstadoConservacao
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
     * @Assert\Length(max=144)
     * @ORM\Column(type="string", length=140, nullable=true)
     */
    private $descricao;

    /**
     * @ORM\OneToMany(targetEntity=Especie::class, mappedBy="estado_conservacao")
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
            $this->descricao = (string) u($descricao)->trim();
        } else {
            $this->descricao = null;
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
            $especy->setEstadoConservacao($this);
        }

        return $this;
    }

    public function removeEspecy(Especie $especy): self
    {
        if ($this->especies->removeElement($especy)) {
            // set the owning side to null (unless already changed)
            if ($especy->getEstadoConservacao() === $this) {
                $especy->setEstadoConservacao(null);
            }
        }

        return $this;
    }
}
