<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Correo Electrónico',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ingrese su correo electrónico',
                    'class' => 'form-control',
                ],
            ])
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ingrese su nombre',
                    'maxlength' => 10,
                    'class' => 'form-control'
                ]
            ])
            ->add('apellido_paterno', TextType::class, [
                'label' => 'Apellido Paterno',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ingrese su Apellido Paterno',
                    'maxlength' => 10,
                    'class' => 'form-control'
                ]
            ])
            ->add('apellido_materno', TextType::class, [
                'label' => 'Apellido Materno',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ingrese su Apellido Materno',
                    'maxlength' => 10,
                    'class' => 'form-control'
                ]
            ])

            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Contraseña',
                'mapped' => false,
                'attr' => [
                    'class'=> 'form-control',
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Ingrese su nombre',                   
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor ingrese una contraseña',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'su contraseña debe tener al menos {{ limit }} caracteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // enable/disable CSRF protection for this form
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'usuario_create',
        ]);
    }
}
