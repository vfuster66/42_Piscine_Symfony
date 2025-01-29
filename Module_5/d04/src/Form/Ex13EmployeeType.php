<?php
// src/Form/Ex13EmployeeType.php

namespace App\Form;

use App\Entity\Ex13Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Ex13EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('email', EmailType::class)
            ->add('birthdate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('active')
            ->add('employedSince', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('employedUntil', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('hours', ChoiceType::class, [
                'choices' => [
                    '8 heures' => '8',
                    '6 heures' => '6',
                    '4 heures' => '4'
                ]
            ])
            ->add('salary', IntegerType::class)
            ->add('position', ChoiceType::class, [
                'choices' => [
                    'Manager' => 'manager',
                    'Account Manager' => 'account_manager',
                    'QA Manager' => 'qa_manager',
                    'Dev Manager' => 'dev_manager',
                    'CEO' => 'ceo',
                    'COO' => 'coo',
                    'Backend Developer' => 'backend_dev',
                    'Frontend Developer' => 'frontend_dev',
                    'QA Tester' => 'qa_tester'
                ]
            ])
            ->add('manager', EntityType::class, [
                'class' => Ex13Employee::class,
                'choice_label' => function ($employee) {
                    return $employee->getFirstname() . ' ' . $employee->getLastname();
                },
                'required' => false,
                'placeholder' => 'Sélectionnez un manager'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ex13Employee::class,
        ]);
    }
}