<?php

namespace App\Form;

use App\Entity\Ex12BankAccount;
use App\Entity\Ex12Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class Ex12BankAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('person', EntityType::class, [
                'class' => Ex12Person::class,
                'choice_label' => 'username', // Affiche le nom d'utilisateur
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->leftJoin('p.bankAccount', 'b')
                        ->where('b.id IS NULL'); // Filtre les personnes sans compte bancaire
                },
                'label' => 'Sélectionnez une personne',
                'placeholder' => 'Choisir une personne',
            ])
            ->add('accountNumber', TextType::class, [
                'label' => 'Numéro de compte',
            ])
            ->add('balance', MoneyType::class, [
                'label' => 'Solde',
                'currency' => 'EUR',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ex12BankAccount::class,
        ]);
    }
}
