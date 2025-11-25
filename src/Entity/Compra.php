<?php

namespace App\Entity;

use App\Repository\CompraRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;       // Ajusta según tu clase de usuario
use App\Entity\Proveedor;  // Nueva entidad Proveedor
use App\Entity\DetalleCompra;

#[ORM\Entity(repositoryClass: CompraRepository::class)]
class Compra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: 'float')]
    private ?float $total = null;

    // Relación con Usuario
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    // Relación con Proveedor
    #[ORM\ManyToOne(targetEntity: Proveedor::class, inversedBy: 'compras')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Proveedor $proveedor = null;

    // Relación inversa con DetalleCompra
    #[ORM\OneToMany(mappedBy: 'compra', targetEntity: DetalleCompra::class, cascade: ['persist', 'remove'])]
    private Collection $detalleCompras;

    public function __construct()
    {
        $this->detalleCompras = new ArrayCollection();
    }

    // Getters y setters básicos
    public function getId(): ?int
    {
        return $this->id;
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

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    // Relación con Usuario
    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    // Relación con Proveedor
    public function getProveedor(): ?Proveedor
    {
        return $this->proveedor;
    }

    public function setProveedor(?Proveedor $proveedor): self
    {
        $this->proveedor = $proveedor;
        return $this;
    }

    // Relación con DetalleCompra
    public function getDetalleCompras(): Collection
    {
        return $this->detalleCompras;
    }

    public function addDetalleCompra(DetalleCompra $detalleCompra): self
    {
        if (!$this->detalleCompras->contains($detalleCompra)) {
            $this->detalleCompras[] = $detalleCompra;
            $detalleCompra->setCompra($this);
        }
        return $this;
    }

    public function removeDetalleCompra(DetalleCompra $detalleCompra): self
    {
        if ($this->detalleCompras->removeElement($detalleCompra)) {
            if ($detalleCompra->getCompra() === $this) {
                $detalleCompra->setCompra(null);
            }
        }
        return $this;
    }
}