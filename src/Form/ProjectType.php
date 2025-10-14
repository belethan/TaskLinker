<?php
namespace App\Form;

use App\Entity\Projet;
use App\Entity\Employe;
use App\Repository\EmployeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $projetId = $options['projet_id'];
        $employesSelectionnes = $options['employes_selectiones'] ?? [];
        // Convertir les IDs en objets Employe
        $employeObjects = [];
        if (!empty($employesSelectionnes) && $options['employe_repo'] instanceof EmployeRepository) {
            $repo = $options['employe_repo'];
            $employeObjects = $repo->findBy(['id' => $employesSelectionnes]);
        }

        $builder
            ->add('nom')
            ->add('employes', EntityType::class, [
                'class' => Employe::class,
                'choice_label' => fn(Employe $e) => $e->getNom() . ' ' . $e->getPrenom(),
                'multiple' => true,
                'expanded' => false,
                'by_reference' => false,
                'choice_value' => 'id',
                'query_builder' => fn(EmployeRepository $r) => $r->findEmployesDisponiblesOuAffectesNative(
                    $projetId,
                    array_map(static fn($e) => $e->getId(), $employeObjects)
                ),
                'attr' => [
                    'class' => 'select2-ajax',
                    'data-ajax-url' => $options['ajax_url'],
                    'data-projet-id' => $projetId ?? '',
                    'data-placeholder' => 'Sélectionnez des employés',
                    // 👇 Ajout propre du data-selected
                    'data-selected' => implode(',', array_map(static fn($e) => $e->getId(), $employeObjects)),
                ],
                'choice_attr' => fn(Employe $e) => [
                    'data-nom' => $e->getNom(),
                    'data-prenom' => $e->getPrenom(),
                ],
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'button button-submit'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Projet::class,
            'projet_id' => null,
            'ajax_url' => '',
            'employes_selectiones' => [],
            'employe_repo' => null,
        ]);

        $resolver->setAllowedTypes('projet_id', ['null', 'int', 'string']);
        $resolver->setAllowedTypes('ajax_url', 'string');
        $resolver->setAllowedTypes('employes_selectiones', 'array');
        $resolver->setAllowedTypes('employe_repo', ['null', EmployeRepository::class]);
    }
    }
