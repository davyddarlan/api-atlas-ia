<?php

namespace App\Entity;

use App\Repository\MetaDadoRepository;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\String\u;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MetaDadoRepository::class)
 */
class MetaDado
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
     * @ORM\Column(type="string", length=255)
     */
    private $nome;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255)
     */
    private $valor;

    /**
     * @ORM\ManyToOne(targetEntity=Multimidia::class, inversedBy="metadado")
     * @ORM\JoinColumn(nullable=false)
     */
    private $multimidia;

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

    public function getValor(): ?string
    {
        return $this->valor;
    }

    public function setValor(string $valor): self
    {
        $this->valor = (string) u($valor)->trim()->lower()->title(true);

        return $this;
    }

    public function getMultimidia(): ?Multimidia
    {
        return $this->multimidia;
    }

    public function setMultimidia(?Multimidia $multimidia): self
    {
        $this->multimidia = $multimidia;

        return $this;
    }
}
