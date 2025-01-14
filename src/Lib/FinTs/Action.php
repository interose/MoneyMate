<?php

namespace App\Lib\FinTs;

enum Action: string
{
    case Login = 'login';
    case GetTanModes = 'getTanModes';
    case GetTanMedia = 'getTanMedia';
    case GetAllAccounts = 'getAllAccounts';
    case CheckDecoupled = 'checkDecoupledSubmission';
    case SubmitTan = 'submitTan';
    case Success = 'success';
    case GetStatementOfAccount = 'getStatementOfAccount';
}
