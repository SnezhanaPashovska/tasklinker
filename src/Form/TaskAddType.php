<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Status;
use App\Entity\Task;
use App\Repository\EmployeeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $project = $options['project'];

        $builder
            ->add('title', null, [
                'label' => 'Titre de la tâche',
            ])

            ->add('description')

            ->add('deadline', null, [
                'widget' => 'single_text',
                'label' => 'Date',
            ])

            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'label',
                'label' => 'Statut',
            ])

            ->add('employees', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => function (Employee $employee) {
                    return $employee->getName() . ' ' . $employee->getLastName();
                },
                'label' => 'Membre',

                'query_builder' => function (EmployeeRepository $er) use ($project) {
                    return $er->createQueryBuilder('e')
                        ->innerJoin('e.projects', 'p')
                        ->where('p.id = :project')
                        ->setParameter('project', $project->getId());
                },
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'project' => null,
        ]);
    }
}
