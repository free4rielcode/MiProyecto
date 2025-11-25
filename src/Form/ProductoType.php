<?php

namespace App\Form;

use App\Entity\Producto;
use App\Entity\Categoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            // Campo de categoría
            ->add('categoria', EntityType::class, [
                'class' => Categoria::class,
                'choice_label' => 'nombre',
                'required' => false,  // Permite null
                'placeholder' => 'Selecciona una categoría',
            ])
            ->add('nombre')
            ->add('descripcion')
            ->add('precio', null, [
                'required' => true,
                'attr' => ['min' => 0],
                'label' => 'Precio compra',
            ])
            
            ->add('precioVenta', null, [
                'required' => true,
                'attr' => ['min' => 0],
                'label' => 'Precio venta',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
        ]);
    }
}
