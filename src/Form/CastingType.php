<?php

namespace App\Form;

use App\Entity\Casting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CastingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('role', TextType::class, [
                'empty_data' => '', 
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min'        => 3,
                        'max'        => 100,
                        'minMessage' => 'Pas assez de caractères (min attendu : {{ limit }})',
                        'maxMessage' => 'Trop caractères (max attendu : {{ limit }})',
                    ])
                ]
            ])
            ->add('orderCredit', IntegerType::class, [
                'attr' => [
                    'min' => 1
                ]
            ])
            ->add('person')
            //->add('movie') //cf casting à partir de movie
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Casting::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}