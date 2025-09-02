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
                $currentUser = $options['current_user'];
        
                return $er->createQueryBuilder('u')
                    ->where('u != :currentUser')
                    ->andWhere('u.id NOT IN (
                        SELECT IDENTITY(f1.user1) FROM App\Entity\Friendships f1 WHERE f1.user2 = :currentUser
                    )')
                    ->andWhere('u.id NOT IN (
                        SELECT IDENTITY(f2.user2) FROM App\Entity\Friendships f2 WHERE f2.user1 = :currentUser
                    )')
                    ->setParameter('currentUser', $currentUser);
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
