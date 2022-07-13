<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use function Symfony\Component\String\u;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{   
    const ATIVO = 1;
    const INATIVO = 2;
    const PENDENTE = 3;
    const TEMPO_TOKEN = 60 * 60 * 24;
    const TEMPO_CONTA = 60 * 60 * 24 * 7;
    const TEMPO_PASSWORD = 60 * 30;
    
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\Email
     * @Assert\NotBlank
     * @Assert\Length(max=180)
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @Assert\NotBlank
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255)
     */
    private $primeiro_nome;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @ORM\Column(type="string", length=255)
     */
    private $sobrenome;

    /**
     * @Assert\NotBlank
     * @Assert\Length(max=1)
     * @Assert\Choice({"M", "F"})
     * @ORM\Column(type="string", length=1)
     */
    private $sexo;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $time_token;

    /**
     * @ORM\Column(type="datetimetz", nullable=true)
     */
    private $time_account;

    /**
     * @ORM\Column(type="date")
     */
    private $data_nascimento;

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private $change_password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $change_password_time;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = (string) u($email)->trim()->lower();

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrimeiroNome(): ?string
    {
        return $this->primeiro_nome;
    }

    public function setPrimeiroNome(string $primeiro_nome): self
    {
        $this->primeiro_nome = (string) u($primeiro_nome)->trim()->lower()->title(true);

        return $this;
    }

    public function getSobrenome(): ?string
    {
        return $this->sobrenome;
    }

    public function setSobrenome(string $sobrenome): self
    {
        $this->sobrenome = (string) u($sobrenome)->trim()->lower()->title(true);

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(string $sexo): self
    {
        $this->sexo = (string) u($sexo)->trim()->upper();

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTimeToken(): ?\DateTimeInterface
    {
        return $this->time_token;
    }

    public function setTimeToken(?\DateTimeInterface $time_token): self
    {
        $this->time_token = $time_token;

        return $this;
    }

    public function getTimeAccount(): ?\DateTimeInterface
    {
        return $this->time_account;
    }

    public function setTimeAccount(?\DateTimeInterface $time_account): self
    {
        $this->time_account = $time_account;

        return $this;
    }

    public function getDataNascimento(): ?\DateTimeInterface
    {
        return $this->data_nascimento;
    }

    public function setDataNascimento(\DateTimeInterface $data_nascimento): self
    {
        $this->data_nascimento = $data_nascimento;

        return $this;
    }

    public function getChangePassword(): ?string
    {
        return $this->change_password;
    }

    public function setChangePassword(?string $change_password): self
    {
        $this->change_password = $change_password;

        return $this;
    }

    public function getChangePasswordTime(): ?\DateTimeInterface
    {
        return $this->change_password_time;
    }

    public function setChangePasswordTime(?\DateTimeInterface $change_password_time): self
    {
        $this->change_password_time = $change_password_time;

        return $this;
    }
}
