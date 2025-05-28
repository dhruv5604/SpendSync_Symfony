<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\Income;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IncomeTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('amount',NumberType::class)
            ->add('category',ChoiceType::class,[
                'choices' => [
                    'Business Income' => 'Business Income',
                    'Salary' => 'Salary',
                    'Freelance work' => 'Freelance work',
                    'Investments' => 'Investments',
                    'Rental Income' => 'Rental Income',
                    'Other' => 'Other'
                ]
            ])
            ->add('account', EntityType::class,[
                'class' => Account::class,
                'choice_label' => 'name',    
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('i')
                            ->where('i.user= :user')
                            ->setParameter('user',$user);
                },
                'placeholder' => 'Select an account',
            ])
            ->add('description',TextareaType::class,['required'=>false])
            ->add('income_date',DateType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Income::class,
            'user' => null
        ]);
    }
}
