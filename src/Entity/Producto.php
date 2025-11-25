<?php

namespace App\Entity;

use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Categoria;
use App\Entity\DetalleCompra;
use App\Entity\DetalleVenta;

#[ORM\Entity(repositoryClass: ProductoRepository::class)]
class Producto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $precio = null;

    #[ORM\Column(type: 'float')]
    private ?float $precioVenta = null;

    #[ORM\Column(type: 'integer')]
    private ?int $stock = null;

    // Relación con Categoria
    #[ORM\ManyToOne(targetEntity: Categoria::class, inversedBy: 'productos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    // Relación inversa con DetalleCompra
    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: DetalleCompra::class)]
    private Collection $detalleCompras;

    // Relación inversa con DetalleVenta
    #[ORM\OneToMany(mappedBy: 'producto', targetEntity: DetalleVenta::class, orphanRemoval: true)]
    private Collection $detalleVentas;

    public function __construct()
    {
        $this->detalleCompras = new ArrayCollection();
        $this->detalleVentas = new ArrayCollection();
        $this->stock = 0;
        $this->precio = 0.0;
    }

    // Métodos básicos
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getPrecio(): ?float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): self
    {
        $this->precio = $precio;
        return $this;
    }

    public function getPrecioVenta(): ?float
    {
        return $this->precioVenta;
    }

    public function setPrecioVenta(float $precioVenta): self
    {
        $this->precioVenta = $precioVenta;
        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;
        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function getNombreCategoria(): ?string
    {
        return $this->categoria?->getNombre();
    }

    public function __toString(): string
    {
        return (string) $this->getNombre();
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
            $detalleCompra->setProducto($this);
        }
        return $this;
    }

    public function removeDetalleCompra(DetalleCompra $detalleCompra): self
    {
        if ($this->detalleCompras->removeElement($detalleCompra)) {
            if ($detalleCompra->getProducto() === $this) {
                $detalleCompra->setProducto(null);
            }
        }
        return $this;
    }

    // Relación con DetalleVenta
    public function getDetalleVentas(): Collection
    {
        return $this->detalleVentas;
    }

    public function addDetalleVenta(DetalleVenta $detalleVenta): self
    {
        if (!$this->detalleVentas->contains($detalleVenta)) {
            $this->detalleVentas[] = $detalleVenta;
            $detalleVenta->setProducto($this);
        }
        return $this;
    }

    public function removeDetalleVenta(DetalleVenta $detalleVenta): self
    {
        if ($this->detalleVentas->removeElement($detalleVenta)) {
            if ($detalleVenta->getProducto() === $this) {
                $detalleVenta->setProducto(null);
            }
        }
        return $this;
    }
}
