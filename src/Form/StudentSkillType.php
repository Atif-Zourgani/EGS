<?php

namespace App\Form;

use App\Entity\Discipline;
use App\Entity\DisciplineLevel;
use App\Entity\Section;
use App\Entity\Skill;
use App\Entity\Student;
use App\Entity\StudentSkill;
use App\Repository\DisciplineLevelRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentSkillType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('discipline', EntityType::class, [
                'class'         => Discipline::class,
                'placeholder'   => 'Sélectionnez une discipline',
                'mapped'        => false,
                'required'      => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StudentSkill::class,
        ]);
    }

}
