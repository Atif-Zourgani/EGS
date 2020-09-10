<?php

namespace App\Form;

use App\Entity\Skill;
use App\Entity\StudentSkill;
use App\Repository\SkillRepository;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentEvaluationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $discipline = $options['discipline'];
        $student = $options['student'];

        $builder
            ->add('skills', EntityType::class, [
                'class' => Skill::class,
                'label' => false,
                'multiple' => true,
                'required' => true,
                'mapped'   => true,
                'expanded' => true,
                'query_builder' => function(SkillRepository $repository) use ($discipline, $student) {
                    return $repository->findByDiscipline($discipline, $student);
                }
            ])
            ->add('comment', CollectionType::class, [
                'entry_type' => CommentType::class,
                'label' => false,
                'required' => false
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StudentSkill::class,
            'discipline' => null,
            'student' => null
        ]);
    }

}
