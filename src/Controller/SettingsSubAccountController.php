<?php

namespace App\Controller;

use App\Entity\SubAccount;
use App\Form\SubAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/subaccount')]
class SettingsSubAccountController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_settings_subaccount_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SubAccount $subAccount, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubAccountType::class, $subAccount, [
            'action' => $this->generateUrl('app_settings_subaccount_edit', ['id' => $subAccount->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Subaccount updated');

            return $this->redirectToRoute('app_settings_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_subaccount/edit.html.twig', [
            'subAccount' => $subAccount,
            'form' => $form,
        ]);
    }
}
