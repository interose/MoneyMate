<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountStep1Type;
use App\Form\AccountStep2Type;
use App\Form\AccountStep3Type;
use App\Lib\FinTs\FinTsAdapter;
use App\Lib\FinTs\FinTsOperation;
use App\Lib\FinTs\FinTsSymfonyAdapter;
use App\Lib\FinTs\TanRequiredException;
use App\Lib\Manager\AccountManager;
use App\Lib\Manager\SubAccountManager;
use App\Repository\AccountRepository;
use App\Repository\SubAccountRepository;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/account')]
class SettingsAccountWizardController extends AbstractController
{
    #[Route('/step1', name: 'app_settings_account_step1', methods: ['GET', 'POST'])]
    public function step1(Request $request, AccountManager $accountManager): Response
    {
        $form = $this->createForm(AccountStep1Type::class, null, [
            'action' => $this->generateUrl('app_settings_account_step1'),
        ]);

        // do not autofill username and password, it has to be reentered by
        // the user every time due to security issues.
        $form->get('username')->setData('');
        $form->get('password')->setData('');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account = $accountManager->saveStep1($form->getData());

            return $this->redirectToRoute('app_settings_account_step2', ['id' => $account->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account_wizard/step1.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/step2', name: 'app_settings_account_step2', methods: ['GET', 'POST'])]
    public function step2(Account $account, Request $request, FinTsSymfonyAdapter $finTsAdapter, AccountManager $accountManager): Response {
        $tanModeChoices = [];

        try {
            $tanModeChoices = $finTsAdapter->getTanModes($account);
        } catch (\Exception $e) {
            $this->addFlash('error', str_replace("\n", '<br>', $e->getMessage()));
        }

        $form = $this->createForm(AccountStep2Type::class, null, [
            'action' => $this->generateUrl('app_settings_account_step2', ['id' => $account->getId()]),
            'tanModeChoices' => $tanModeChoices,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountManager->saveStep2($form->getData(), $account);

            return $this->redirectToRoute('app_settings_account_step3', ['id' => $account->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account_wizard/step2.html.twig', [
            'form' => $form,
            'account' => $account,
        ]);
    }

    #[Route('/{id}/step3', name: 'app_settings_account_step3', methods: ['GET', 'POST'])]
    public function step3(Account $account, Request $request, FinTsSymfonyAdapter $finTsAdapter, SubAccountManager $subAccountManager): Response
    {
        try {
            $sepaAcccounts = $this->loadSepaAccounts($request, $account, $finTsAdapter);
        } catch (TanRequiredException $e) {
            return $this->render('settings_account_wizard/step3.html.twig', [
                'tanRequired' => true,
                'account' => $account,
                'challenge' => $e->getChallenge(),
            ]);
        }

        $subAccounts = $subAccountManager->createOrUpdate($account, $sepaAcccounts);

        return $this->render('settings_account_wizard/step3.html.twig', [
            'tanRequired' => false,
            'account' => $account,
            'subAccounts' => $subAccounts,
        ]);
    }

    #[Route('/{id}/fetch-tan-media', name: 'app_account_fetch_tanmedia', methods: ['GET', 'POST'])]
    public function fetchTanMedia(Request $request, Account $account, FinTsSymfonyAdapter $finTsAdapter): JsonResponse
    {
        if (!$request->query->has('tan-mode')) {
            return new JsonResponse('', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $tanMedia = $finTsAdapter->getTanMedia($account, $request->query->getInt('tan-mode'));
        } catch (\Exception $e) {
            return new JsonResponse('', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse($tanMedia);
    }

    /**
     * @throws TanRequiredException
     */
    private function loadSepaAccounts(Request $request, Account $account, FinTsSymfonyAdapter $finTsAdapter): array
    {
        $session = $request->getSession();
        $key = 'step3_subaccounts_'.$account->getId();

        if ($session->has($key)) {
            return $session->get($key);
        }

        $subAccounts = $finTsAdapter->consumeResult($account, FinTsOperation::Subaccounts)
            ?? $finTsAdapter->getSubaccounts($account); // may still throw TanRequiredException on first-ever visit

        $session->set($key, $subAccounts);

        return $subAccounts;
    }
}
