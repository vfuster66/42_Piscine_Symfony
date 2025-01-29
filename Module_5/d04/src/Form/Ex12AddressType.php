<?php

namespace App\Form;

use App\Entity\Ex12Address;
use App\Entity\Ex12Person;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Ex12AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('person', EntityType::class, [
                'class' => Ex12Person::class,
                'choice_label' => 'username',
                'label' => 'Personne associée',
            ])
            ->add('addressLine', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ex12Address::class,
        ]);
    }
}
