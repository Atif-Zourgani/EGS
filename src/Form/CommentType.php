<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;


class CommentType extends AbstractType
{

    protected $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        //dump($user->getTeam());

        if ($user->getTeacher()) {
            $builder
                ->add('content', TextareaType::class, [
                    'label' => false
                ])
                ->add('rating', ChoiceType::class, [
                    'choices' => [
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5'
                    ],
                    'expanded' => true,
                    'choice_label' => false
                ]);
        } else {
            $builder
                ->add('teacher', EntityType::class, [
                    'class' => Teacher::class,
                    'required' => false,
                    'placeholder' => 'Choisir un intervenant'
                ])
                ->add('content', TextareaType::class, [
                    'label' => false
                ])
                ->add('rating', ChoiceType::class, [
                    'choices' => [
                        '1' => '1',
                        '2' => '2',
                        '3' => '3',
                        '4' => '4',
                        '5' => '5'
                    ],
                    'expanded' => true,
                    'choice_label' => false
                ])
            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
