<?php

namespace App\Entity;

use App\Repository\EspecieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\String\u;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass=EspecieRepository::class)
 * @UniqueEntity("nome_cientifico")
 */
class Especie
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
    private $nome_cientifico;

    /**
     * @Assert\Regex("/^\d{4}$/")
     * @ORM\Column(type="string", length=4, nullable=true)
     */
    private $ano_descoberta;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nome_ingles;

    /**
     * @Assert\Length(max=140)
     * @ORM\Column(type="string", length=140, nullable=true)
     */
    private $descricao;

    /**
     * @ORM\Column(type="guid", unique=true)
     */
    private $uuid;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $principal_nome_popular;

    /**
     * @Assert\Length(max=50)
     * @ORM\Column(type="string", length=50, nullable=true, unique=true)
     */
    private $capa;

    /**
     * @ORM\ManyToMany(targetEntity=NomePopular::class)
     */
    private $nome_popular;

    /**
     * @ORM\ManyToMany(targetEntity=Descobridor::class)
     */
    private $descobridor;

    /**
     * @Assert\File(
     *      maxSize = "1M",
     *      mimeTypes = {"image/jpeg"}
     * )
     */
    private $multimidia_capa;

    /**
     * @ORM\OneToMany(targetEntity=Multimidia::class, mappedBy="especie")
     */
    private $multimidia;

    /**
     * @ORM\ManyToOne(targetEntity=EstadoConservacao::class, inversedBy="especies")
     */
    private $estado_conservacao;

    /**
     * @ORM\ManyToMany(targetEntity=Marcador::class, inversedBy="especies")
     */
    private $marcador;

    /**
     * @ORM\OneToOne(targetEntity=Cladograma::class, cascade={"persist", "remove"})
     */
    private $cladograma;

    public function __construct()
    {
        $this->nome_popular = new ArrayCollection();
        $this->descobridor = new ArrayCollection();
        $this->multimidia = new ArrayCollection();
        $this->marcador = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomeCientifico(): ?string
    {
        return $this->nome_cientifico;
    }

    public function setNomeCientifico(string $nome_cientifico): self
    {
        $this->nome_cientifico =  (string) u($nome_cientifico)->trim()->lower()->title(true);

        return $this;
    }

    public function getAnoDescoberta(): ?string
    {
        return $this->ano_descoberta;
    }

    public function setAnoDescoberta(?string $ano_descoberta): self
    {
        if (!empty($ano_descoberta)) {
            $this->ano_descoberta = (string) u($ano_descoberta)->trim();
        } else {
            $this->ano_descoberta = null;
        }

        return $this;
    }

    public function getNomeIngles(): ?string
    {
        return $this->nome_ingles;
    }

    public function setNomeIngles(?string $nome_ingles): self
    {
        if (!empty($nome_ingles)) {
            $this->nome_ingles = (string) u($nome_ingles)->trim()->lower()->title(true);
        } else {
            $this->nome_ingles = null;
        }
        
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

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Collection<int, NomePopular>
     */
    public function getNomePopular(): Collection
    {
        return $this->nome_popular;
    }

    public function addNomePopular(NomePopular $nomePopular): self
    {
        if (!$this->nome_popular->contains($nomePopular)) {
            $this->nome_popular[] = $nomePopular;
        }

        return $this;
    }

    public function removeNomePopular(NomePopular $nomePopular): self
    {
        $this->nome_popular->removeElement($nomePopular);

        return $this;
    }

    /**
     * @return Collection<int, Descobridor>
     */
    public function getDescobridor(): Collection
    {
        return $this->descobridor;
    }

    public function addDescobridor(Descobridor $descobridor): self
    {
        if (!$this->descobridor->contains($descobridor)) {
            $this->descobridor[] = $descobridor;
        }

        return $this;
    }

    public function removeDescobridor(Descobridor $descobridor): self
    {
        $this->descobridor->removeElement($descobridor);

        return $this;
    }

    public function getPrincipalNomePopular(): ?string
    {
        return $this->principal_nome_popular;
    }

    public function setPrincipalNomePopular(?string $principal_nome_popular): self
    {
        if (!empty($principal_nome_popular)) {
            $this->principal_nome_popular = (string) u($principal_nome_popular)->trim()->lower()->title(true);
        } else {
            $principal_nome_popular = null;
        }
    
        return $this;
    }

    public function getCapa(): ?string
    {
        return $this->capa;
    }

    public function setCapa(?string $capa): self
    {
        $this->capa = $capa;

        return $this;
    }

    /**
     * @return Collection<int, Multimidia>
     */
    public function getMultimidia(): Collection
    {
        return $this->multimidia;
    }

    public function setMultimidiaCapa(File $multimidia_capa)
    {
        $this->multimidia_capa = $multimidia_capa;
    }

    public function getEstadoConservacao(): ?EstadoConservacao
    {
        return $this->estado_conservacao;
    }

    public function setEstadoConservacao(?EstadoConservacao $estado_conservacao): self
    {
        $this->estado_conservacao = $estado_conservacao;

        return $this;
    }

    /**
     * @return Collection<int, Marcador>
     */
    public function getMarcador(): Collection
    {
        return $this->marcador;
    }

    public function addMarcador(Marcador $marcador): self
    {
        if (!$this->marcador->contains($marcador)) {
            $this->marcador[] = $marcador;
        }

        return $this;
    }

    public function removeMarcador(Marcador $marcador): self
    {
        $this->marcador->removeElement($marcador);

        return $this;
    }

    public function getCladograma(): ?Cladograma
    {
        return $this->cladograma;
    }

    public function setCladograma(?Cladograma $cladograma): self
    {
        $this->cladograma = $cladograma;

        return $this;
    }
}