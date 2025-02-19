<?php

namespace App\Form;

use App\Entity\Personne;
use App\Entity\Trajet;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrajetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('villeA')
            ->add('villeD')
            ->add('conducteur')
            ->add('dateTrajet', null, [
                'widget' => 'single_text',
            ])
            ->add('nbrPlaces')
            ->add('personne', EntityType::class, [
                'class' => Personne::class,
                'choice_label' => 'id',
            ])
            ->add('trajet', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'id',
            ])
            ->add('passagers', EntityType::class, [
                'class' => Personne::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trajet::class,
        ]);
    }
}
