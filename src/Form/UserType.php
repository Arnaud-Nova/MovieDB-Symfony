<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

	$listener = function (FormEvent $event) {
// dd($event);
        $user = $event->getData();
        $form = $event->getForm();
        /*
        L'objet du type FormEvent contient deux methodes :

        - getData() qui permet de recuperer les données du formulaire
        - getForm() qui permet de recupèrer le formulaire en tant que tel
        */
        
        // Conditionner la construction du champs password sur selon le contexte de l'objet user (new OU update)
        if(is_null($user)) { // si user id est null => création d'un user donc ajout de la constrainte
            $form->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'empty_data' => '',// ATTENTION : necessaire pour setter une valeur par defaut en cas de chaine sinon NULL ce qui generera une erreur
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password','empty_data' => ''],
                'second_options' => ['label' => 'Repeat Password','empty_data' => '',],
            ]);
        } else {
            //sinon on est en modification donc pas de constraint mais placeholder changé

            $form->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'empty_data' => '',// ATTENTION : necessaire pour setter une valeur par defaut en cas de chaine sinon NULL ce qui generera une erreur
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => [
                    'label' => 'Password',
                    'empty_data' => '',
                    'attr' => [
                        'placeholder' => 'Laisser vide si inchangé'
                    ]
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'empty_data' => '',
                    'attr' => [
                        'placeholder' => 'Laisser vide si inchangé'
                    ]
                ,],
            ]);
        }
        

           
        };

        /*
         Note: actuellement il est possible d'appliquer des contraintes notBlank
         sur les champs prevus a cet effet. Neanmoins cela est bloquant pour mon edition.

         En effet , je vais souhaiter avoir un password obligatoire par ex en creation en revanche ce n'est pas forcement le cas en modification car
         je pourrai souhaiter uniquement modifier l'email d emon user par ex.

         A notre stade nous n'avons pas les billes en main pour avoir une solution dites "propre" pour gerer ce cas
        */
        $builder
            ->add('username',TextType::class, [
                'empty_data' => '', 
            ])
	    /*
            Remplacer la construction champs password par la methode addEventListener() qui prend un parametre:
            - L'event (FormEvents) sur lequel doit s'effectuer l'action 
            - le listener créé
            */
            ->addEventListener( // on ajouter un listener sur l'événement pre set data qui s'occupe de la construction du formulaire
                FormEvents::PRE_SET_DATA,
                $listener
            )   
            ->add('email',EmailType::class, [
                'empty_data' => '', 
            ])
            ->add('isActive')
            ->add('role')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}
