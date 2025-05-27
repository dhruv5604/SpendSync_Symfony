<?php

namespace App\Form;

use App\Entity\Income;
use App\Entity\User;
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
            ->add('description',TextareaType::class,['required'=>false])
            ->add('income_date',DateType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Income::class,
        ]);
    }
}
