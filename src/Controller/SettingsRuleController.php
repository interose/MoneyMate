<?php

namespace App\Controller;

use App\Entity\CategoryAssignmentRule;
use App\Form\CategoryAssignmentRuleType;
use App\Repository\CategoryAssignmentRuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/rules')]
class SettingsRuleController extends AbstractController
{
    #[Route('/', name: 'app_settings_rule_index', methods: ['GET'])]
    public function index(CategoryAssignmentRuleRepository $repository): Response
    {
        return $this->render('settings_rule/index.html.twig', [
            'rules' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_settings_rule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rule = new CategoryAssignmentRule();
        $form = $this->createForm(CategoryAssignmentRuleType::class, $rule, [
            'action' => $this->generateUrl('app_settings_rule_new'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rule);
            $entityManager->flush();

            $this->addFlash('success', 'Assignment Rule created');

            return $this->redirectToRoute('app_settings_rule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_rule/new.html.twig', [
            'rule' => $rule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_rule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoryAssignmentRule $rule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryAssignmentRuleType::class, $rule, [
            'action' => $this->generateUrl('app_settings_rule_edit', ['id' => $rule->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Assignment Rule updated');

            return $this->redirectToRoute('app_settings_rule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_rule/edit.html.twig', [
            'rule' => $rule,
            'form' => $form,
        ]);
    }
}