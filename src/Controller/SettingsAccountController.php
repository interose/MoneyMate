<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/account')]
class SettingsAccountController extends AbstractController
{
    #[Route('/', name: 'app_settings_account_index', methods: ['GET'])]
    public function index(AccountRepository $repository): Response
    {
        return $this->render('settings_account/index.html.twig', [
            'accounts' => $repository->findAll(),
        ]);
    }
}
