<?php

namespace App\Lib\FinTs;

enum Action: string
{
    case GetTanModes = 'getTanModes';
    case GetTanMedia = 'getTanMedia';
    case GetAllAccounts = 'getAllAccounts';
    case CheckDecoupled = 'checkDecoupledSubmission';
}