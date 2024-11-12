<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\TimeEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeConnectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('email')
            ->add('password')
            /*->add('role')
            ->add('contract')
            ->add('active')
            ->add('arrival_date', null, [
                'widget' => 'single_text',
            ])
            ->add('projects', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('timeEntry', EntityType::class, [
                'class' => TimeEntry::class,
                'choice_label' => 'id',
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}
