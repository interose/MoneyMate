<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AccountStep3Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $tanModeChoices = $options['tanModeChoices'] ?? [];

        if (count($tanModeChoices) > 0) {

            $choicesAttr = [];
            $choices = ['Please select' => ''];

            foreach ($tanModeChoices as $tanModeChoice) {
                $choices[$tanModeChoice['name']] = $tanModeChoice['id'];

                $choicesAttr[$tanModeChoice['name']] = [
                    'data-needs-tan-medium' => $tanModeChoice['needsTanMedium'] ? 'true' : 'false',
                    'data-is-decoupled' => $tanModeChoice['isDecoupled'] ? 'true' : 'false',
                ];
            }

            $builder
                ->add('tanMechanism', ChoiceType::class, [
                    'label' => 'TAN Mode',
                    'choices' => $choices,
                    'choice_attr' => $choicesAttr,
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
        $enabled = $data['tanMechanism'] ?? false;
    }
}
