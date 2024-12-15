<?php

namespace App\Form;

use App\Entity\SubAccount;
use App\Lib\Manager\SettingsManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(SettingsManager::SETTING_MAIN_ACCOUNT, EntityType::class, [
                'label' => 'Main Account',
                'required' => true,
                'class' => SubAccount::class,
                'placeholder' => 'Please select an account',
                'choice_label' => function (SubAccount $subAccount) {
                    return $subAccount->getAccount()->getName().' - '.$subAccount->getAccountNumber();
                },
                'query_builder' => function ($er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.enabled = true');
                },
                'empty_data' => [],
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add(SettingsManager::SETTING_STOCK_ACCOUNT_ENABLED, CheckboxType::class)
            ->add(SettingsManager::SETTING_STOCK_PUBLISHER_USER, TextType::class, [
                'label' => 'User',
                'required' => 'false',
            ])
            ->add(SettingsManager::SETTING_STOCK_PUBLISHER_PW, PasswordType::class, [
                'label' => 'Password',
                'required' => 'false',
            ])
            ->add(SettingsManager::SETTING_STOCK_DIVIDEND_URL, UrlType::class, [
                'label' => 'Champion CSV URL',
                'required' => 'false',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => [
                'novalidate' => true,
            ],
            'constraints' => [
                new Callback([$this, 'validateFields']),
            ],
        ]);
    }

    /**
     * Custom validation logic for both fields.
     */
    public function validateFields($data, ExecutionContextInterface $context)
    {
        $enabled = $data['boerseAccountEnabled'] ?? false;
        $user = $data['boersePublisherUser'] ?? null;
        $pw = $data['boersePublisherPw'] ?? null;

        $bothFilled = !empty($user) && !empty($pw);

        if ($enabled && !$bothFilled) {
            $context->buildViolation('If enabled, both fields must be filled.')
                ->atPath('[boersePublisherUser]') // You can highlight field1, field2, or both
                ->addViolation();
            $context->buildViolation('If enabled, both fields must be filled.')
                ->atPath('[boersePublisherPw]')
                ->addViolation();
        }
    }
}
