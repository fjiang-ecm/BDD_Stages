<?php

namespace App\Form;

use App\Entity\Internship;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InternshipType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('city')
            ->add('postalCode')
            ->add('country')
            ->add('company')
            ->add('contact')
            ->add('startedOn')
            ->add('finishedOn')
            ->add('author')
            ->add('category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Internship::class,
        ]);
    }
}