<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\TimeEntry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('deadline', null, [
                'widget' => 'single_text',
            ])
            ->add('projects', EntityType::class, [
                'class' => Project::class,
                'choice_label' => 'id',
            ])
            ->add('employees', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => 'id',
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('status', EntityType::class, [
                'class' => Status::class,
                'choice_label' => 'id',
            ])
            ->add('timeEntry', EntityType::class, [
                'class' => TimeEntry::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
