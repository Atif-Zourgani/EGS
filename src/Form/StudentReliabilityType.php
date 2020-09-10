<?php

namespace App\Form;

use App\Entity\Incident;
use App\Entity\StudentReliability;
use App\Repository\IncidentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentReliabilityType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $type= $options['type'];

        $builder
            ->add('incident', EntityType::class, [
                'class' => Incident::class,
                'label' => false,
                'multiple'      => true,
                'expanded'      => true,
                'required'      => true,
                'mapped'        => true,
                'query_builder' => function(IncidentRepository $repository) use($type) {
                    return $repository->findByType($type);
                }]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StudentReliability::class,
            'type' => null,
        ]);
    }
}
