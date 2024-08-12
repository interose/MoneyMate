<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name of the Bank',
            ])
            ->add('accountHolder', TextType::class, [
                'label' => 'Account holder',
            ])
            ->add('iban', TextType::class, [
                'label' => 'IBAN',
            ])
            ->add('bic', TextType::class, [
                'label' => 'BIC',
            ])
            ->add('bankCode', TextType::class, [
                'label' => 'BLZ',
            ])
            ->add('url', UrlType::class, [
                'label' => 'HBCI / FinTS URL',
            ])
            ->add('tanMediaName', TextType::class, [
                'label' => 'TAN Medium',
            ])
            ->add('tanMechanism', IntegerType::class, [
                'label' => 'TAN Mode',
            ])
            ->add('username', PasswordType::class, [
                'label' => 'User',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
            ])
            ->add('logo', FileType::class, [
                'label' => 'Logo',
                'required' => false,
                'data_class' => null
            ])
            ->add('backgroundColor', ColorType::class, [
                'label' => 'Background',
                'required' => false,
            ])
            ->add('foregroundColor', ColorType::class, [
                'label' => 'Foreground',
                'required' => false,
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