<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriaRepository::class)]
class Categoria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $descripcion = null;

    // Relación jerárquica: categoría padre
    #[ORM\ManyToOne(targetEntity: Categoria::class, inversedBy: 'subcategorias')]
    #[ORM\JoinColumn(name: 'categoria_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Categoria $categoria = null;

    // Relación jerárquica: subcategorías
    #[ORM\OneToMany(mappedBy: 'categoria', targetEntity: Categoria::class)]
    private Collection $subcategorias;

    // Relación inversa con DetalleCompra
    #[ORM\OneToMany(mappedBy: 'categoria', targetEntity: DetalleCompra::class)]
    private Collection $detalleCompras;

    public function __construct()
    {
        $this->subcategorias = new ArrayCollection();
        $this->detalleCompras = new ArrayCollection();
    }

    // Getters y setters básicos
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

    // Padre
    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;
        return $this;
    }

    // Subcategorías
    public function getSubcategorias(): Collection
    {
        return $this->subcategorias;
    }

    public function addSubcategoria(Categoria $subcategoria): self
    {
        if (!$this->subcategorias->contains($subcategoria)) {
            $this->subcategorias[] = $subcategoria;
            $subcategoria->setCategoria($this);
        }
        return $this;
    }

    public function removeSubcategoria(Categoria $subcategoria): self
    {
        if ($this->subcategorias->removeElement($subcategoria)) {
            if ($subcategoria->getCategoria() === $this) {
                $subcategoria->setCategoria(null);
            }
        }
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
            $detalleCompra->setCategoria($this);
        }
        return $this;
    }

    public function removeDetalleCompra(DetalleCompra $detalleCompra): self
    {
        if ($this->detalleCompras->removeElement($detalleCompra)) {
            if ($detalleCompra->getCategoria() === $this) {
                $detalleCompra->setCategoria(null);
            }
        }
        return $this;
    }
}