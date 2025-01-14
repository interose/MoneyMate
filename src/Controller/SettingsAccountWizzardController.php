<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountStep1Type;
use App\Form\AccountStep2Type;
use App\Form\AccountStep3Type;
use App\Form\AccountStep4Type;
use App\Lib\FinTs\Action;
use App\Lib\FinTs\TanRequiredException;
use App\Lib\FinTs\Wrapper;
use App\Lib\LogoUploader;
use App\Lib\SubAccountUpdater;
use App\Repository\AccountRepository;
use App\Repository\SubAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/account/wizzard')]
class SettingsAccountWizzardController extends AbstractController
{
    #[Route('/step1', name: 'app_settings_account_step1', methods: ['GET', 'POST'])]
    public function step1(Request $request, AccountRepository $repository, EntityManagerInterface $entityManager): Response
    {
        $account = new Account();
        $formUrl = $this->generateUrl('app_settings_account_step1');

        if ($request->query->has('id')) {
            $tmpAccount = $repository->findOneById($request->query->getInt('id'));
            if (!is_null($tmpAccount)) {
                $account = $tmpAccount;
                $formUrl = $this->generateUrl('app_settings_account_step1', ['id' => $account->getId()]);
            }
        }

        $form = $this->createForm(AccountStep1Type::class, $account, [
            'action' => $formUrl,
            'validation_groups' => 'step1',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($account);
            $entityManager->flush();

            return $this->redirectToRoute('app_settings_account_step2', ['id' => $account->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account_wizzard/step1.html.twig', [
            'form' => $form,
            'account' => $account,
        ]);
    }

    #[Route('/{id}/step2', name: 'app_settings_account_step2', methods: ['GET', 'POST'])]
    public function step2(
        Request $request,
        Account $account,
        AccountRepository $repository,
        ParameterBagInterface $bag,
    ): Response {
        $form = $this->createForm(AccountStep2Type::class, $account, [
            'action' => $this->generateUrl('app_settings_account_step2', ['id' => $account->getId()]),
            'validation_groups' => 'step2',
        ]);

        // do not autofill username and password, it has to be reentered by the user every time due to security issues.
        $form->get('username')->setData('');
        $form->get('password')->setData('');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $key = $bag->get('encryption_key');
            if (0 === strlen($key)) {
                throw new \Exception('Encryption key not set!');
            }

            $repository->saveEncrypted($account->getId(), $account->getUsername(), $account->getPassword(), $key);

            return $this->redirectToRoute('app_settings_account_step3', ['id' => $account->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account_wizzard/step2.html.twig', [
            'form' => $form,
            'account' => $account,
        ]);
    }

    #[Route('/{id}/step3', name: 'app_settings_account_step3', methods: ['GET', 'POST'])]
    public function step3(
        Request $request,
        Account $account,
        EntityManagerInterface $entityManager,
        Wrapper $finTsWrapper,
    ): Response {
        $tanModeChoices = [];
        $formFinTsErrorMessage = '';

        try {
            $tanModeChoices = $finTsWrapper->getTanModeChoices($account);
        } catch (\Exception $e) {
            $formFinTsErrorMessage = str_replace("\n", '<br>', $e->getMessage());
        }

        $form = $this->createForm(AccountStep3Type::class, $account, [
            'action' => $this->generateUrl('app_settings_account_step3', ['id' => $account->getId()]),
            'validation_groups' => 'step3',
            'tanModeChoices' => $tanModeChoices,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($account);
            $entityManager->flush();

            return $this->redirectToRoute('app_settings_account_step4', ['id' => $account->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account_wizzard/step3.html.twig', [
            'form' => $form,
            'formFinTsErrorMessage' => $formFinTsErrorMessage,
            'account' => $account,
        ]);
    }

    #[Route('/{id}/step4', name: 'app_settings_account_step4', methods: ['GET', 'POST'])]
    public function step4(
        Request $request,
        Account $account,
        Wrapper $finTsWrapper,
        SubAccountUpdater $updater,
        SubAccountRepository $repository): Response
    {
        try {
            $subAccounts = $finTsWrapper->getAllSubaccounts($account, $request->query->get('action'));
            $updater->createOrUpdate($account, $subAccounts);
        } catch (TanRequiredException $e) {
            return $this->render('settings_account_wizzard/step4ConfirmTanMedia.html.twig', [
                'account' => $account,
                'action' => Action::CheckDecoupled->value,
                'msg' => 'Please confirm the action on your device and then click OK.',
            ]);
        } catch (\Exception $e) {
            return $this->render('settings_account_wizzard/step4Error.html.twig', [
                'account' => $account,
                'error' => str_replace("\n", '<br>', $e->getMessage()),
            ]);
        }

        $subAccounts = $repository->findBy(['account' => $account]);

        $form = $this->createForm(AccountStep4Type::class, $account, [
            'action' => $this->generateUrl('app_settings_account_step5', ['id' => $account->getId()]),
            'validation_groups' => 'step4',
        ]);

        return $this->render('settings_account_wizzard/step4.html.twig', [
            'account' => $account,
            'subAccounts' => $subAccounts,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/step5', name: 'app_settings_account_step5', methods: ['POST'])]
    public function step5(Request $request, Account $account, EntityManagerInterface $entityManager, LogoUploader $uploader): Response
    {
        $form = $this->createForm(AccountStep4Type::class, $account, [
            'action' => $this->generateUrl('app_settings_account_step5', ['id' => $account->getId()]),
            'validation_groups' => 'step4',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logoFile')->getData();

            if ($logoFile) {
                $filename = $uploader->upload($logoFile);
                $account->setLogo($filename);
            }

            $entityManager->persist($account);
            $entityManager->flush();

            $this->addFlash('success', 'Account updated');
        }

        return $this->redirectToRoute('app_settings_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
