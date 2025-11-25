<?php

namespace App\Form;
use App\Entity\Proveedor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Compra;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use App\Form\DetalleCompraType;

class CompraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('proveedor', EntityType::class, [
                'class' => Proveedor::class,
                'choice_label' => 'nombre', // o el campo que quieras mostrar
                'label' => 'Proveedor',
                'placeholder' => 'Seleccione un proveedor',
            ])

            ->add('detalleCompras', CollectionType::class, [
                'entry_type' => DetalleCompraType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Productos en la compra',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Compra::class,
        ]);
    }
}
