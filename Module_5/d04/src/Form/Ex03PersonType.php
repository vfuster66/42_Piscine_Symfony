<?php

namespace App\Form;

use App\Entity\Ex03Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Ex03PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['label' => 'Nom d\'utilisateur'])
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('email', TextType::class, ['label' => 'Email'])
            ->add('enable', CheckboxType::class, ['label' => 'Activer', 'required' => false])
            ->add('birthdate', DateType::class, ['label' => 'Date de naissance', 'widget' => 'single_text'])
            ->add('address', TextareaType::class, ['label' => 'Adresse']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ex03Person::class,
        ]);
    }
}
