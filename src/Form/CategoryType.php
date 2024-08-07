<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\CategoryGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use TalesFromADev\FlowbiteBundle\Form\Type\SwitchType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categoryGroup', EntityType::class, [
                'label' => 'Group',
                'class' => CategoryGroup::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Please select',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('treeIgnore', SwitchType::class, [
                'label' => 'Tree Ignore',
                'required' => false
            ])
            ->add('dashboardIgnore', SwitchType::class, [
                'label' => 'Dashboard Ignore',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'attr' => [
                'novalidate' => true,
            ],
        ]);
    }
}
