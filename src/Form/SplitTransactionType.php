<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\SplitTransaction;
use App\Repository\CategoryRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class SplitTransactionType extends AbstractType
{
    public function __construct(private readonly CategoryRepository $categoryRepository) {}


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description', TextType::class, [
                'constraints' => new NotBlank(),
                'empty_data' => '',
            ])
            ->add('category', HiddenType::class, [
                'constraints' => new NotBlank(),
                'attr' => ['id' => 'category-hidden-input'],
                'mapped' => false
            ])
            ->add('amount', MoneyType::class, [
                'currency' => 'EUR',
                'html5' => false,
                'divisor' => 100,
                'input' => 'integer',
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(0),
                ],
                'empty_data' => 0,
            ])
        ;

        // Resolve the category ID → entity after submission
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $splitTransaction = $event->getData();

            $categoryId = $form->get('category')->getData();
            if ($categoryId) {
                $category = $this->categoryRepository->find($categoryId);
                if ($category) {
                    $splitTransaction->setCategory($category);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SplitTransaction::class,
        ]);
    }
}
