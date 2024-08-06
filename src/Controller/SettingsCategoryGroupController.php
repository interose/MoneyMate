<?php

namespace App\Controller;

use App\Entity\CategoryGroup;
use App\Form\CategoryGroupType;
use App\Repository\CategoryGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/category-group')]
class SettingsCategoryGroupController extends AbstractController
{
    #[Route('/', name: 'app_settings_category_group_index', methods: ['GET'])]
    public function index(CategoryGroupRepository $repository): Response
    {
        return $this->render('settings_category_group/index.html.twig', [
            'categoryGroups' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_settings_category_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoryGroup = new CategoryGroup();
        $form = $this->createForm(CategoryGroupType::class, $categoryGroup, [
            'action' => $this->generateUrl('app_settings_category_group_new'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoryGroup);
            $entityManager->flush();

            $this->addFlash('success', 'Category Group created');

            return $this->redirectToRoute('app_settings_category_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_category_group/new.html.twig', [
            'categoryGroup' => $categoryGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_category_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoryGroup $categoryGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryGroupType::class, $categoryGroup, [
            'action' => $this->generateUrl('app_settings_category_group_edit', ['id' => $categoryGroup->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Category Group updated');

            return $this->redirectToRoute('app_settings_category_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_category_group/edit.html.twig', [
            'categoryGroup' => $categoryGroup,
            'form' => $form,
        ]);

    }
}
