<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Compra;
use App\Entity\Categoria;
use App\Entity\DetalleCompra;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DetalleCompraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder


             ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
                'label' => 'Categoria',
                'placeholder' => 'Seleccione una Categoria',
                'attr' => ['class' => 'form-select shadow-sm'],
                'mapped' => true,
            ])
            ->add('productoNombre', null, [
                'attr' => ['class' => 'form-control shadow-sm'],
                'label' => 'Nombre del Producto',
                'required' => true,
                'mapped' => false,
            ])
            ->add('productoDescripcion', null, [
                'attr' => ['class' => 'form-control shadow-sm'],
                'label' => 'Descripción del Producto',
                'required' => true,
                'mapped' => false,
            ])
            ->add('cantidad', null, [
                'attr' => ['class' => 'form-control shadow-sm'],
                'label' => 'Cantidad',
            ])
            ->add('precioUnitario', null, [
                'attr' => ['class' => 'form-control shadow-sm'],
                'label' => 'Precio unitario',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DetalleCompra::class,
        ]);
    }
}
