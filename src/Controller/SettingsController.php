<?php

namespace App\Controller;

use App\Form\SettingsType;
use App\Lib\Manager\SettingsManager;
use App\Repository\AccountRepository;
use App\Service\EncryptionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings')]
class SettingsController extends AbstractController
{
    #[Route('/', name: 'app_settings_index', methods: ['GET', 'POST'])]
    public function index(SettingsManager $settingsManager): Response
    {
        return $this->render('settings/index.html.twig', [
            'settings' => $settingsManager->all(),
        ]);
    }

    #[Route('/accounts', name: 'app_settings_accounts_index', methods: ['GET'])]
    public function accounts(AccountRepository $repository, EncryptionService $encryptionService): Response
    {
        return $this->render('settings/accounts.html.twig', [
            'accounts' => $repository->findAll(),
        ]);
    }

    #[Route('/edit', name: 'app_settings_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SettingsManager $settingsManager): Response
    {
        $form = $this->createForm(SettingsType::class, $settingsManager->all());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settingsManager->setMany($form->getData());

            $this->addFlash('success', 'Settings updated!');

            return $this->redirectToRoute('app_settings_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
