<?php

namespace App\Form;

use App\Entity\Account;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        $type = $options['type'];

        $builder
            ->add('amount')
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('transaction_date', DateType::class)
            ->add('account', EntityType::class, [
                'class' => Account::class,
                'choice_label' => 'name',
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('a')
                        ->where('a.user = :user')
                        ->setParameter('user', $user);
                },
                'placeholder' => 'Select an account',
            ])
        ;

        if ($type === 'expense') {
            $builder->add('category', ChoiceType::class, [
                'choices' => [
                    'Food' => 'Food',
                    'Travel' => 'Travel',
                    'Bills' => 'Bills',
                    'Others' => 'Others',
                ],
            ]);
        } else {
            $builder->add('category', ChoiceType::class, [
                'choices' => [
                    'Business Income' => 'Business Income',
                    'Salary' => 'Salary',
                    'Freelance work' => 'Freelance work',
                    'Investments' => 'Investments',
                    'Rental Income' => 'Rental Income',
                    'Other' => 'Other'
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
        $resolver->setDefined(['user', 'type']);
        $resolver->setAllowedTypes('user', [User::class, 'null']);
        $resolver->setAllowedTypes('type', 'string');
    }
}
