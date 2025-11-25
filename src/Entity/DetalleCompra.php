<?php

namespace App\Entity;

use App\Repository\DetalleCompraRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DetalleCompraRepository::class)]
#[ORM\Table(name: 'detalle_compra')]
class DetalleCompra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'detalleCompras')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Compra $compra = null;

    #[ORM\ManyToOne(targetEntity: Producto::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Producto $producto = null;

    #[ORM\ManyToOne(targetEntity: Categoria::class, inversedBy: 'detalleCompras')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categoria $categoria = null;

    #[ORM\Column]
    private ?int $cantidad = null;

    #[ORM\Column]
    private ?float $precioUnitario = null;

    #[ORM\Column]
    private ?int $subtotal = null;

    private ?string $productoNombre = null;

    private ?string $productoDescripcion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompra(): ?Compra
    {
        return $this->compra;
    }

    public function setCompra(?Compra $compra): static
    {
        $this->compra = $compra;

        return $this;
    }



    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): static
    {
        $this->producto = $producto;
        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): static
    {
        $this->categoria = $categoria;
        return $this;
    }

    public function getProductoNombre(): ?string
    {
        return $this->productoNombre;
    }

    public function setProductoNombre(?string $productoNombre): static
    {
        $this->productoNombre = $productoNombre;
        return $this;
    }

    public function getProductoDescripcion(): ?string
    {
        return $this->productoDescripcion;
    }

    public function setProductoDescripcion(?string $productoDescripcion): static
    {
        $this->productoDescripcion = $productoDescripcion;
        return $this;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): static
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getPrecioUnitario(): ?float
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario(float $precioUnitario): static
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    public function getSubtotal(): ?int
    {
        return $this->subtotal;
    }

    public function setSubtotal(int $subtotal): static
    {
        $this->subtotal = $subtotal;

        return $this;
    }
}
