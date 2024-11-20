<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Prénom',
            ])
            ->add('last_name', null, [
                'label' => 'Nom',
            ])
            ->add('email', null, [
                'label' => 'Email',
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Employe' => 0,
                    'Admin' => 1,
                ],
                'label' => 'Rôle',
                'expanded' => false,
                'multiple' => false,
            ])
            ->add('arrival_date', null, [
                'label' => 'Date d\'entrée',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('contract', null, [
                'label' => 'Statut',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
            'csrf_protection' => false,
        ]);
    }
}
