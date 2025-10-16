<?php

namespace App\Form;

use App\Entity\Taches;
use App\Entity\Employe;
use App\Enum\StatutTache;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\EmployeRepository;

class TacheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var int|null $projetId */
        $projetId = $options['projet_id'];

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de la tâche',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
            ])
            ->add('deadline', DateType::class, [
                'label' => 'Date limite',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('statutTache', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'To do' => StatutTache::TODO,
                    'Doing' => StatutTache::DOING,
                    'Done' => StatutTache::DONE,
                ],
                'placeholder' => false,  // pas de placeholder, pour forcer la sélection
                'required' => true,
            ])

            ->add('employe', EntityType::class, [
                'class' => Employe::class,
                'label' => 'Employé assigné',
                'required' => true,
                'choice_label' => fn(Employe $e) => $e->getPrenom() . ' ' . $e->getNom(),
                'query_builder' => function (EmployeRepository $repo) use ($projetId) {
                    return $repo->createQueryBuilder('e')
                        ->join('e.projets', 'p')
                        ->where('p.id = :projetId')
                        ->setParameter('projetId', $projetId);
                },
                'placeholder' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Taches::class,
            'projet_id' => null, // 👈 paramètre personnalisé
        ]);
    }
}

