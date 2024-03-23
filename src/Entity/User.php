<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`users`")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\SequenceGenerator(sequenceName="users_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    private $plainPassword;
    
    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_cre", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateCre;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_maj", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $dateMaj;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_cre", type="bigint", nullable=true, options={"default"="1"})
     */
    private $userCre = '1';

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_maj", type="bigint", nullable=true, options={"default"="1"})
     */
    private $userMaj = '1';
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDateCre(): ?\DateTimeInterface
    {
        return $this->dateCre;
    }

    public function setDateCre(?\DateTimeInterface $dateCre): static
    {
        $this->dateCre = $dateCre;

        return $this;
    }

    public function getDateMaj(): ?\DateTimeInterface
    {
        return $this->dateMaj;
    }

    public function setDateMaj(?\DateTimeInterface $dateMaj): static
    {
        $this->dateMaj = $dateMaj;

        return $this;
    }

    public function getUserCre(): ?string
    {
        return $this->userCre;
    }

    public function setUserCre(?string $userCre): static
    {
        $this->userCre = $userCre;

        return $this;
    }

    public function getUserMaj(): ?string
    {
        return $this->userMaj;
    }

    public function setUserMaj(?string $userMaj): static
    {
        $this->userMaj = $userMaj;

        return $this;
    }

}
