<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\Expense;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpenseTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('amount')
            ->add('category', ChoiceType::class,[
                'choices' => [
                    'Food' => 'Food',
                    'Travel' => 'Travel',
                    'Bills' => 'Bills',
                    'Others' => 'Others',
                ]
            ])
            ->add('account', EntityType::class,[
                'class' => Account::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('a')
                            ->where('a.user= :user',)
                            ->setParameter('user',$user);
                },
                'placeholder' => 'Select an account',
            ])
            ->add('expense_date', DateType::class)
            ->add('description', TextareaType::class,['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expense::class,
            'user' => null
        ]);
    }
}
