<?php

namespace App\Form;

use App\Entity\Student;
use App\Entity\StudentCall;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentCallType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'disabled' => true,
                'label' => function ($student) {
                    return $student->getFullname();
                }
            ])
            ->add('status', ChoiceType::class, [
                    'choices' => [
                        'Présent' => 'present',
                        'En retard' => 'late',
                        'Absent' => 'absent',
                        'Absence justifiée' => 'justified'
                    ],
                    'expanded' => true,
                    'choice_label' => false
                ]
            )
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StudentCall::class
        ]);
    }
}
