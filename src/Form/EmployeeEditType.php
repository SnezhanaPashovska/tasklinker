<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\TimeEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',null,[
                'label' => 'Prénom',
            ])
            ->add('last_name', null, [
                'label' => 'Nom',
            ])
            ->add('email', null, [
                'label' => 'Email',
            ])
            /*->add('role', null, [
                'label' => 'Rôle',
            ])*/
            ->add('arrival_date', null, [
                'label' => 'Date d\'entrée',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('contract', null, [
                'label' => 'Statut',
            ])
            /*->add('active', null, [
                'label' => 'Actif',
            ])*/
            
              /*->add('projects', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])*/
            /*->add('timeEntry', EntityType::class, [
                'class' => TimeEntry::class,
                'choice_label' => 'id',
                'label' => 'Entrée de temps',
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
