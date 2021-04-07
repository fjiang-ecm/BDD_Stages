<?php

namespace App\Form;

use App\Entity\Internship;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('startedOn', DateType::class,[
                "format"=>"dd/MM/yyyy",
                'widget' => 'single_text',
                'label' => 'Date de début'
            ])
            ->add('finishedOn',DateType::class, [
                "format"=>"dd/MM/yyyy",
                'widget' => 'single_text',
                'label' => 'Date de fin'
            ])
            ->add('category' )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Internship::class,
        ]);
    }
}