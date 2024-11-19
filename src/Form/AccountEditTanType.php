<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Webmozart\Assert\Assert;

class AccountEditTanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name of the Bank',
                'attr' => [
                    'placeholder' => ' ',
                ],
            ])
            ->add('bic', TextType::class, [
                'label' => 'BIC',
                'attr' => [
                    'placeholder' => ' ',
                ],
            ])
            ->add('bankCode', TextType::class, [
                'label' => 'BLZ',
                'attr' => [
                    'placeholder' => ' ',
                ],
            ])
            ->add('url', UrlType::class, [
                'label' => 'HBCI / FinTS URL',
                'attr' => [
                    'placeholder' => ' ',
                ],
            ])
            ->add('logoFile', FileType::class, [
                'label' => 'Logo',
                'mapped' => false,
                'required' => false,
                'help' => 'SVG, PNG, JPG or GIF (MAX. 1024x1024).',
            ])
            ->add('foregroundColor', ColorType::class, [
                'label' => 'Foreground',
            ])
            ->add('backgroundColor', ColorType::class, [
                'label' => 'Background',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
            'attr' => [
                'novalidate' => true,
            ],
        ]);
    }
}
