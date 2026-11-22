<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

final class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('username', TextType::class, [
            'label' => 'Nombre de usuario',
            'required' => false,
            'constraints' => [
                new NotBlank(
                    message: 'El nombre de usuario no debe estar vacío.',
                    normalizer: 'trim',
                ),
                new Regex(
                    pattern: '/^[\p{L}]+(?: [\p{L}]+)*$/u',
                    message: 'El nombre solo puede contener letras y espacios.',
                    normalizer: 'trim',
                ),
            ],
            'attr' => [
                'autocomplete' => 'off',
                'placeholder' => 'Escribe tu nombre',
                'maxlength' => 50,
                'required' => false,
            ],
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
