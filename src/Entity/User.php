<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
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
     * @ORM\OneToMany(targetEntity=Chat::class, mappedBy="user")
     */
    private $chats;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cliente;

    /**
     * @ORM\Column(type="boolean")
     */
    private $admin;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="users")
     */
    private $grupo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;


    public function __construct()
    {
        $this->chats = new ArrayCollection();
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

       

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if ($this->admin == 1 ){
            $roles = ['ROLE_USER','ROLE_ADMIN'];
        } else{
            if ($this->cliente == 1 ){
                $roles = ['ROLE_USER','ROLE_CLIENT'];
            }
            $roles[] = 'ROLE_USER';
        }

        

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Chat[]
     */
    public function getChats(): Collection
    {
        return $this->chats;
    }

    public function addChat(Chat $chat): self
    {
        if (!$this->chats->contains($chat)) {
            $this->chats[] = $chat;
            $chat->setUser($this);
        }

        return $this;
    }

    public function removeChat(Chat $chat): self
    {
        if ($this->chats->contains($chat)) {
            $this->chats->removeElement($chat);
            // set the owning side to null (unless already changed)
            if ($chat->getUser() === $this) {
                $chat->setUser(null);
            }
        }

        return $this;
    }

    public function getCliente(): ?bool
    {
        return $this->cliente;
    }

    public function setCliente(bool $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function getAdmin(): ?bool
    {
        return $this->admin;
    }

    public function setAdmin(bool $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

   
    public function getGrupo(): ?Group
    {
        return $this->grupo;
    }

    public function setGrupo(?Group $grupo): self
    {
        $this->grupo = $grupo;

        return $this;
    }

    public function __toString()
    {
        // to show the name of the Category in the select
        return $this->username;
        // to show the id of the Category in the select
        // return $this->id;
    }

   

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    


}
