<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountEditBasicType;
use App\Form\AccountStep2Type;
use App\Form\AccountStep3Type;
use App\Lib\FinTs\Action;
use App\Lib\FinTs\Factory;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
        SluggerInterface $slugger,
        EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%/public/uploads/logos')] string $logosDirectory,
    ): Response {
        $form = $this->createForm(AccountEditBasicType::class, $account, [
            'action' => $this->generateUrl('app_settings_account_edit_basic', ['id' => $account->getId()]),
            'validation_groups' => ['step1', 'step4'],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logoFile')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($logoFile) {
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logoFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $logoFile->move($logosDirectory, $newFilename);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $account->setLogo($newFilename);
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
        Factory $finTsFactory,
    ): Response {
        $formHasFinTsError = false;
        $formFinTsErrorMessage = '';

        try {
            $finTs = $finTsFactory->getFinTs($account);
            $finTsAction = new \stdClass();
            $finTsAction->action = Action::GetTanModes;

            $tanModeChoices = ['Please select' => ''];
            array_map(function ($item) use (&$tanModeChoices) {
                $tanModeChoices[$item->getName()] = $item->getId();
            }, $finTs->handleAction($finTsAction));
        } catch (\Exception $e) {
            $tanModeChoices = [];
            $formHasFinTsError = true;
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
            'formHasFinTsError' => $formHasFinTsError,
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

        $form->handleAction($request);

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
    public function fetchTanMedia(Request $request, Account $account, Factory $finTsFactory): JsonResponse
    {
        if (!$request->query->has('tan-mode')) {
            return new JsonResponse('', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $finTsAction = new \stdClass();
        $finTsAction->action = Action::GetTanMedia;
        $finTsAction->tanMode = $request->query->getInt('tan-mode');

        try {
            $finTs = $finTsFactory->getFinTs($account);
            $tanMedia = array_map(function ($medium) {
                return ['name' => $medium->getName(), 'phoneNumber' => $medium->getPhoneNumber()];
            }, $finTs->handleAction($finTsAction));
        } catch (\Exception $e) {
            return new JsonResponse('', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse($tanMedia);
    }
}
