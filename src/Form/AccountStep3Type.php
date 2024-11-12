<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountStep3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tanModeChoices = $options['tanModeChoices'] ?? [];

        if (count($tanModeChoices) > 0) {
            $builder
                ->add('tanMechanism', ChoiceType::class, [
                    'label' => 'TAN Mode',
                    'choices' => $tanModeChoices,
                ])
                ->add('tanMediaName', ChoiceType::class, [
                    'label' => 'TAN Medium',
                    'attr' => [
                        'disabled' => 'disabled',
                        'class' => 'cursor-not-allowed',
                    ],
                    'choices' => [
                        'Please select' => '',
                    ],
                ]);

            // this is needed because the possible choices are added via javascript
            $builder->get('tanMediaName')->resetViewTransformers();
        } else {
            $builder
                ->add('tanMechanism', TextType::class, [
                    'label' => 'TAN Mode',
                    'attr' => [
                        'placeholder' => ' ',
                    ],
                ])
                ->add('tanMediaName', TextType::class, [
                    'label' => 'TAN Medium',
                    'attr' => [
                        'placeholder' => ' ',
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
            'attr' => [
                'novalidate' => true,
            ],
            'tanModeChoices' => [],
        ]);
    }
}
