<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/category')]
class SettingsCategoryController extends AbstractController
{
    #[Route('/', name: 'app_settings_category_index', methods: ['GET'])]
    public function index(CategoryRepository $repository): Response
    {
        return $this->render('settings_category/index.html.twig', [
            'categories' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_settings_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('app_settings_category_new'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created');

            return $this->redirectToRoute('app_settings_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_category/new.html.twig', [
            'categoryGroup' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('app_settings_category_edit', ['id' => $category->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Category updated');

            return $this->redirectToRoute('app_settings_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }
}
