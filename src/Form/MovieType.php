<?php

namespace App\Form;

use App\Entity\Movie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
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
            ->add('summary', TextareaType::class, [
                'empty_data' => '', 
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min'        => 10,
                        'max'        => 255,
                        'minMessage' => 'Pas assez de caractères (min attendu : {{ limit }})',
                        'maxMessage' => 'Trop caractères (max attendu : {{ limit }})',
                    ])
                ]
            ])
            ->add('poster', FileType::class,[
                'label' => 'Affiche (jpg,png,gif)'
            ])
            ->add('genres', null, [
                'help' => 'Choix multiple possible',
                //'multiple' => true,
                //'expanded' => true,
            ])
        ;

        /*
         Si je souhaite afficher ma liste différemment a savoir : 
            - select box (classique)
            - radio button
            - choix multiple dans checkbox
            - checkbox
         Je dois imperativement utiliser le type ChoiceType et non Checkbox

         Le visuel est condtionné par les 2 propriété suivantes : multiple et expanded

         Ici un tableau recap pour afficher ce que l'on souhaite : https://symfony.com/doc/current/reference/forms/types/choice.html#select-tag-checkboxes-or-radio-buttons

         Merci Franck & Antoine <3
        */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
