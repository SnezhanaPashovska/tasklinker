<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Repository\EmployeeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Titre du projet',
            ])

            ->add('employees', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => function (Employee $employee) {
                    return $employee->getName() . ' ' . $employee->getLastName();
                },
                'multiple' => true,
                'label' => 'Inviter des membres',

                'query_builder' => function (EmployeeRepository $er) {
                    return $er->createQueryBuilder('e')
                        ->where('e.active = :active')
                        ->setParameter('active', true);
                },
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
