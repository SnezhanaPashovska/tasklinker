<?php

namespace App\Form;

use App\Entity\Employee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use App\Repository\EmployeeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;



class EmployeeAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('last_name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['min' => 8]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Employee::class,
        ]);
    }
}