<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CategoryAssignmentRule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryAssignmentRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('rule', TextType::class, [
                'label' => 'Rule',
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => CategoryAssignmentRule::AVAILABLE_TYPE_CHOICES,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Please select',
                'required' => true,
            ])
            ->add('transactionField', ChoiceType::class, [
                'label' => 'Transaction field',
                'choices' => CategoryAssignmentRule::AVAILABLE_TRANSACTION_FIELD_CHOICE,
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Please select',
                'required' => true,
            ])
            ->add('category', EntityType::class, [
                'label' => 'Category',
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Please select',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CategoryAssignmentRule::class,
            'attr' => [
                'novalidate' => true,
            ],
        ]);
    }
}
