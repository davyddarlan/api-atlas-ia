<?php

namespace App\Entity;

use App\Repository\MultimidiaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use function Symfony\Component\String\u;

/**
 * @ORM\Entity(repositoryClass=MultimidiaRepository::class)
 */
class Multimidia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $titulo;

    /**
     * @Assert\Length(max=140)
     * @ORM\Column(type="string", length=140, nullable=true)
     */
    private $descricao;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    private $nome;

    /**
     * @Assert\NotBlank
     * @Assert\File(
     *      maxSize = "10M",
     *      mimeTypes = {"image/png", "image/jpeg", "audio/mpeg", "video/mp4" }
     * )
     */
    private $multimidia;

    /**
     * @Assert\NotBlank
     * @ORM\ManyToOne(targetEntity=Especie::class, inversedBy="multimidia")
     */
    private $especie;

    /**
     * @ORM\OneToMany(targetEntity=MetaDado::class, mappedBy="multimidia", orphanRemoval=true)
     */
    private $metadado;

    public function __construct()
    {
        $this->metaDados = new ArrayCollection();
        $this->metadado = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return empty($this->titulo) ? '' : $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        if (!empty($titulo)) {
            $this->titulo = (string) u($titulo)->trim()->lower()->title(true);
        } else {
            $this->titulo = null;
        }

        return $this;
    }

    public function getDescricao(): ?string
    {
        return empty($this->descricao) ? '' : $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        if (!empty($descricao)) {
            $this->descricao = (string) u($descricao)->trim();
        } else {
            $this->descricao = null;
        }

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getEspecie(): ?Especie
    {
        return $this->especie;
    }

    public function setEspecie(?Especie $especie): self
    {
        $this->especie = $especie;

        return $this;
    }

    public function setMultimidia(File $multimidia)
    {
        $this->multimidia = $multimidia;
    }

    /**
     * @return Collection<int, MetaDado>
     */
    public function getMetadado(): Collection
    {
        return $this->metadado;
    }

    public function addMetadado(MetaDado $metadado): self
    {
        if (!$this->metadado->contains($metadado)) {
            $this->metadado[] = $metadado;
            $metadado->setMultimidia($this);
        }

        return $this;
    }

    public function removeMetadado(MetaDado $metadado): self
    {
        if ($this->metadado->removeElement($metadado)) {
            // set the owning side to null (unless already changed)
            if ($metadado->getMultimidia() === $this) {
                $metadado->setMultimidia(null);
            }
        }

        return $this;
    }
}
