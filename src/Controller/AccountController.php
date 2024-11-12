<?php

namespace App\Controller;

use App\Lib\FinTs\Factory;
use App\Lib\FinTs\TanRequiredException;
use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account')]
class AccountController extends AbstractController
{
    #[Route('/test', name: 'app_account_test')]
    public function index(Request $request, Factory $finTsFactory, AccountRepository $accountRepository): Response
    {
        $account = $accountRepository->findOneBy(['id' => 1]);

        try {
            $tan = $request->request->get('tan');
            $finTsAccountHandler = $finTsFactory->getFinTs($account);
            $finTsAccountHandler->getSepaAccounts($tan);
        } catch (TanRequiredException $e) {
            return new Response('Tan Required');
        } catch (\Exception $e) {
            return new Response('Exception: '.$e->getMessage());
        }

        return new Response('Test');
    }
}