<?php

namespace App\Twig\Components;

use App\Entity\Account;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class AccountWizzardStepper
{
    public int $currentStep = 1;
    public Account $account;

    public array $steps = [[
        'title' => 'Bank Info',
        'description' => '',
        'routeName' => 'app_settings_account_step1',
    ], [
        'title' => 'Online Credentials',
        'description' => '',
        'routeName' => 'app_settings_account_step2',
    ], [
        'title' => 'TAN Methods',
        'description' => '',
        'routeName' => 'app_settings_account_step3',
    ], [
        'title' => 'Final',
        'description' => '',
        'routeName' => 'app_settings_account_step4',
    ]];
}
