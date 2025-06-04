<?php

namespace App\Form;

use App\Entity\Friendships;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FriendshipTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('user2', EntityType::class, [
            'class' => User::class,
            'choice_label' => 'username',
            'required' => true,
            'query_builder' => function (EntityRepository $er) use ($options) {
                return $er->createQueryBuilder('u')
                    ->where('u != :currentUser')
                    ->setParameter('currentUser', $options['current_user']);
            },
            'attr' => ['class' => 'select2'],
        ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Family' => 'family',
                    'Friends' => 'friends',
                    'Colleagues' => 'colleagues',
                    'Acquaintances' => 'acquaintances',
                ],
                'placeholder' => 'Select a category',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Friendships::class,
            'current_user' => null,
        ]);
    }
}
