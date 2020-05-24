<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TicketRepository::class)
 */
class Ticket
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $cliente;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $empleado;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $grupo;

    /**
     * @ORM\ManyToOne(targetEntity=Estado::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $estado;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $asunto;

    /**
     * @ORM\Column(type="text")
     */
    private $mensaje;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $respuesta;

    /**
     * @ORM\OneToMany(targetEntity=HistoricoTicket::class, mappedBy="ticket")
     */
    private $historicoTickets;



    public function __construct()
    {
        $this->historicoTickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCliente(): ?User
    {
        return $this->cliente;
    }

    public function setCliente(?User $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function getEmpleado(): ?User
    {
        return $this->empleado;
    }

    public function setEmpleado(?User $empleado): self
    {
        $this->empleado = $empleado;

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

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getAsunto(): ?string
    {
        return $this->asunto;
    }

    public function setAsunto(string $asunto): self
    {
        $this->asunto = $asunto;

        return $this;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): self
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getRespuesta(): ?string
    {
        return $this->respuesta;
    }

    public function setRespuesta(?string $respuesta): self
    {
        $this->respuesta = $respuesta;

        return $this;
    }

    public function __toString()
    {
        // to show the name of the Category in the select
        return $this->id;
        // to show the id of the Category in the select
        // return $this->id;
    }

    /**
     * @return Collection|HistoricoTicket[]
     */
    public function getHistoricoTickets(): Collection
    {
        return $this->historicoTickets;
    }

    public function addHistoricoTicket(HistoricoTicket $historicoTicket): self
    {
        if (!$this->historicoTickets->contains($historicoTicket)) {
            $this->historicoTickets[] = $historicoTicket;
            $historicoTicket->setTicket($this);
        }

        return $this;
    }

    public function removeHistoricoTicket(HistoricoTicket $historicoTicket): self
    {
        if ($this->historicoTickets->contains($historicoTicket)) {
            $this->historicoTickets->removeElement($historicoTicket);
            // set the owning side to null (unless already changed)
            if ($historicoTicket->getTicket() === $this) {
                $historicoTicket->setTicket(null);
            }
        }

        return $this;
    }



}
