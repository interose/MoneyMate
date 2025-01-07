<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountEditBasicType;
use App\Form\AccountStep2Type;
use App\Form\AccountStep3Type;
use App\Lib\FinTs\Action;
use App\Lib\FinTs\Factory;
use App\Lib\FinTs\Wrapper;
use App\Lib\LogoUploader;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/account')]
class SettingsAccountController extends AbstractController
{
    #[Route('/', name: 'app_settings_account_index', methods: ['GET'])]
    public function index(AccountRepository $repository, Request $request): Response
    {
        return $this->render('settings_account/index.html.twig', [
            'accounts' => $repository->findAll(),
            'editedAccountId' => $request->query->getInt('editedAccountId'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_account_edit_basic', methods: ['POST', 'GET'])]
    public function editBasic(
        Account $account,
        Request $request,
        EntityManagerInterface $entityManager,
        LogoUploader $uploader,
    ): Response {
        $form = $this->createForm(AccountEditBasicType::class, $account, [
            'action' => $this->generateUrl('app_settings_account_edit_basic', ['id' => $account->getId()]),
            'validation_groups' => ['step1', 'step4'],
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

            return $this->redirectToRoute('app_settings_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account/editBasic.html.twig', [
            'form' => $form,
            'account' => $account,
        ]);
    }

    #[Route('/{id}/edit-tan', name: 'app_settings_account_edit_tan', methods: ['POST', 'GET'])]
    public function editTan(
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
            'action' => $this->generateUrl('app_settings_account_edit_tan', ['id' => $account->getId()]),
            'validation_groups' => 'step3',
            'tanModeChoices' => $tanModeChoices,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($account);
            $entityManager->flush();

            $this->addFlash('success', 'Account updated');

            return $this->redirectToRoute('app_settings_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account/editTan.html.twig', [
            'form' => $form,
            'formFinTsErrorMessage' => $formFinTsErrorMessage,
            'account' => $account,
        ]);
    }

    #[Route('/{id}/edit-credentials', name: 'app_settings_account_edit_credentials', methods: ['POST', 'GET'])]
    public function editCredentials(
        Request $request,
        Account $account,
        AccountRepository $repository,
        ParameterBagInterface $bag,
    ): Response {
        $form = $this->createForm(AccountStep2Type::class, $account, [
            'action' => $this->generateUrl('app_settings_account_edit_credentials', ['id' => $account->getId()]),
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

            return $this->redirectToRoute('app_settings_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account/editCredentials.html.twig', [
            'form' => $form,
            'account' => $account,
        ]);
    }

    #[Route('/{id}/fetch-tan-media', name: 'app_settings_account_fetch_tan_media', methods: ['GET', 'POST'])]
    public function fetchTanMedia(Request $request, Account $account, Wrapper $finTsWrapper): JsonResponse
    {
        if (!$request->query->has('tan-mode')) {
            return new JsonResponse('', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $tanMedia = $finTsWrapper->getTanMediaChoices($account, $request->query->getInt('tan-mode'));
        } catch (\Exception $e) {
            return new JsonResponse('', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse($tanMedia);
    }
}
