<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SplitTransaction;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SplitTransactionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'constraints' => new NotBlank(),
                'empty_data' => '',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    if ($category->getCategoryGroup()) {
                        return $category->getCategoryGroup()->getName().':'.$category->getName();
                    } else {
                        return $category->getName();
                    }
                },
                'multiple' => false,
                'expanded' => false,
                'query_builder' => function (CategoryRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.categoryGroup, c.name', 'ASC');
                },
                'placeholder' => 'Choose a category',
                'autocomplete' => true,
                'constraints' => new NotBlank()
            ])
            ->add('amount', MoneyType::class, [
                'getter' => function(SplitTransaction $splitTransaction, FormInterface $form): string {
                    return $splitTransaction->getAmountAsCurrency();
                },
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(0)
                ],
                'empty_data' => 0,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SplitTransaction::class,
        ]);
    }
}