<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CategoryGroup;
use App\Form\DataTransformer\GroupToNumberTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use TalesFromADev\FlowbiteBundle\Form\Type\SwitchType;

class CategoryType extends AbstractType
{
    public function __construct(private readonly GroupToNumberTransformer $transformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categoryGroup', HiddenType::class)
            ->add('name', TextType::class, [
                'label' => 'Name',
                'attr' => [
                    'placeholder' => ' ',
                ],
            ])
            ->add('treeIgnore', SwitchType::class, [
                'label' => 'Tree Ignore',
                'required' => false,
            ])
            ->add('dashboardIgnore', SwitchType::class, [
                'label' => 'Dashboard Ignore',
                'required' => false,
            ])
        ;

        $builder->get('categoryGroup')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'attr' => [
                'novalidate' => true,
            ],
            'group_id' => null,
        ]);
    }
}
