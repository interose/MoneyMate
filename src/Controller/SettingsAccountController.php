<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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

    #[Route('/new', name: 'app_settings_account_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/logos')] string $logosDirectory): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account, [
            'action' => $this->generateUrl('app_settings_account_new'),
            'validation_groups' => 'new'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $logoFile */
            $logoFile = $form->get('logo')->getData();

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

                $account->setLogo($newFilename);
            }

            $entityManager->persist($account);
            $entityManager->flush();

            $this->addFlash('success', 'Account created');

            return $this->redirectToRoute('app_settings_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account/new.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_account_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Account $account, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AccountType::class, $account, [
            'action' => $this->generateUrl('app_settings_account_new'),
            'validation_groups' => 'edit'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Account updated');

            return $this->redirectToRoute('app_settings_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_account/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
