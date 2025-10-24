<?php

namespace App\Form;

use App\Entity\Employe;
use App\Enum\TypeContrat;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EmployeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'label_attr' => [
                    'class' => 'required'
                ],
                'attr' => [
                    'maxlength' => 150,
                    'placeholder' => 'Entrez le nom'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le nom est obligatoire'
                    ]),
                    new Assert\Length([
                        'max' => 150,
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
                'attr' => [
                    'maxlength' => 100,
                    'placeholder' => 'Entrez le prénom'
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'Le prénom ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'label_attr' => [
                    'class' => 'required'
                ],
                'attr' => [
                    'maxlength' => 255,
                    'placeholder' => 'exemple@email.com'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'L\'email est obligatoire'
                    ]),
                    new Assert\Email([
                        'message' => 'L\'email {{ value }} n\'est pas valide'
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'L\'email ne peut pas dépasser {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('password', PasswordType::class, [
                'required' => false, // ⚠️ pour ne pas forcer la saisie
                'mapped' => false,   // car on va l’encoder manuellement dans le contrôleur
                'label' => 'Mot de passe',
                'attr' => [
                    'autocomplete' => 'new-password',
                    'placeholder' => 'Laisser vide pour conserver l’ancien mot de passe'
                ],
            ])
            ->add('dateEntree', DateTimeType::class, [
                'label' => 'Date d\'entrée',
                'required' => true,
                'label_attr' => [
                    'class' => 'required'
                ],
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date d\'entrée est obligatoire'
                    ])
                ]
            ])
            ->add('typeContrat', EnumType::class, [
                'label' => 'Type de contrat',
                'class' => typeContrat::class,
                'required' => true,
                'label_attr' => [
                    'class' => 'required'
                ],
                'attr' => ['class' => 'select2'], // important pour le JS
                'placeholder' => 'Sélectionnez un type de contrat',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le type de contrat est obligatoire'
                    ])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => [
                    'class' => 'button button-submit'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employe::class,
        ]);
    }
}
