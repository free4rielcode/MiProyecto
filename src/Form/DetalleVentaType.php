<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Venta;
use App\Entity\Producto;
use App\Entity\Categoria;
use App\Entity\DetalleVenta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetalleVentaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Campo para seleccionar la categoría del producto (no mapeado a la entidad)
        $builder->add('categoria', EntityType::class, [
            'class' => Categoria::class,
            'choice_label' => 'nombre',
            'mapped' => false,
            'placeholder' => 'Seleccione una categoría',
            'attr' => ['class' => 'form-select shadow-sm'],
            'required' => true,
        ]);

        // Campo para seleccionar el producto, que será filtrado por la categoría seleccionada mediante JS
        $builder->add('producto', EntityType::class, [
            'class' => Producto::class,
            'choice_label' => 'nombre',
            'placeholder' => 'Seleccione un producto',
            'attr' => ['class' => 'form-select shadow-sm'],
            'required' => true,
        ]);

        // Campo para ingresar la cantidad del producto
        $builder->add('cantidad', null, [
            'attr' => ['class' => 'form-control shadow-sm', 'min' => 1],
            'label' => 'Cantidad',
            'required' => true,
        ]);

        // Campo para mostrar el precio sugerido del producto seleccionado, se actualizará automáticamente con JS
        $builder->add('precioSugerido', null, [
            'label' => 'Precio sugerido',
            'attr' => ['class' => 'form-control shadow-sm', 'readonly' => true],
            'required' => false,
            'mapped' => false,
        ]);

        // Campo editable para el precio final de venta
        $builder->add('precioUnitario', null, [
            'label' => 'Precio final',
            'attr' => ['class' => 'form-control shadow-sm', 'min' => 0, 'step' => '0.01'],
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DetalleVenta::class,
        ]);
    }
}
