<?php

namespace App\Entity;

use App\Repository\CladogramaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CladogramaRepository::class)
 */
class Cladograma
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Reino::class)
     */
    private $reino;

    /**
     * @ORM\ManyToOne(targetEntity=Filo::class)
     */
    private $filo;

    /**
     * @ORM\ManyToOne(targetEntity=Divisao::class)
     */
    private $divisao;

    /**
     * @ORM\ManyToOne(targetEntity=Classe::class)
     */
    private $classe;

    /**
     * @ORM\ManyToOne(targetEntity=SubClasse::class)
     */
    private $subclasse;

    /**
     * @ORM\ManyToOne(targetEntity=Ordem::class)
     */
    private $ordem;

    /**
     * @ORM\ManyToOne(targetEntity=Familia::class)
     */
    private $familia;

    /**
     * @ORM\ManyToOne(targetEntity=SubFamilia::class)
     */
    private $subfamilia;

    /**
     * @ORM\ManyToOne(targetEntity=Genero::class)
     */
    private $genero;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReino(): ?Reino
    {
        return $this->reino;
    }

    public function setReino(?Reino $reino): self
    {
        $this->reino = $reino;

        return $this;
    }

    public function getFilo(): ?Filo
    {
        return $this->filo;
    }

    public function setFilo(?Filo $filo): self
    {
        $this->filo = $filo;

        return $this;
    }

    public function getDivisao(): ?Divisao
    {
        return $this->divisao;
    }

    public function setDivisao(?Divisao $divisao): self
    {
        $this->divisao = $divisao;

        return $this;
    }

    public function getClasse(): ?Classe
    {
        return $this->classe;
    }

    public function setClasse(?Classe $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getSubclasse(): ?SubClasse
    {
        return $this->subclasse;
    }

    public function setSubclasse(?SubClasse $subclasse): self
    {
        $this->subclasse = $subclasse;

        return $this;
    }

    public function getOrdem(): ?Ordem
    {
        return $this->ordem;
    }

    public function setOrdem(?Ordem $ordem): self
    {
        $this->ordem = $ordem;

        return $this;
    }

    public function getFamilia(): ?Familia
    {
        return $this->familia;
    }

    public function setFamilia(?Familia $familia): self
    {
        $this->familia = $familia;

        return $this;
    }

    public function getSubfamilia(): ?SubFamilia
    {
        return $this->subfamilia;
    }

    public function setSubfamilia(?SubFamilia $subfamilia): self
    {
        $this->subfamilia = $subfamilia;

        return $this;
    }

    public function getGenero(): ?Genero
    {
        return $this->genero;
    }

    public function setGenero(?Genero $genero): self
    {
        $this->genero = $genero;

        return $this;
    }
}
