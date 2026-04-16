<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CategoryGroup;
use App\Form\CategoryType;
use App\Repository\CategoryGroupRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/settings/category')]
class SettingsCategoryController extends AbstractController
{
    #[Route('/index', name: 'app_settings_categories_by_group', methods: ['GET'])]
    public function getCategoriesByGroupId(Request $request, CategoryGroupRepository $repository): Response
    {
        /** @var CategoryGroup $group */
        $group = $repository->findOneById($request->query->get('id'));

        return $this->render('settings_category/index.html.twig', [
            'group' => $group,
            'categories' => $group->getCategories(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_settings_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryGroup $group, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $category->setCategoryGroup($group);

        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('app_settings_category_new', ['id' => $group->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'Category created');

            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('settings_category/rowAppend.stream.html.twig', [
                'category' => $category
            ]);
        }

        return $this->render('settings_category/new.html.twig', [
            'category' => $category,
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

            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('settings_category/rowUpdate.stream.html.twig', [#
                'category' => $category
            ]);
        }

        return $this->render('settings_category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

        $isValidToken = $this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->getString('_token'));

        if ($isValidToken) {
            if ($category->getTransactions()->count() > 0) {
                $this->addFlash('error', 'Category is already used! Could not delete it!');
            } else {
                $id = $category->getId();
                $entityManager->remove($category);
                $entityManager->flush();

                $this->addFlash('success', 'Category deleted!');

                return $this->render('settings_category/rowDelete.stream.html.twig', [
                    'id' => $id
                ]);
            }
        } else {
            $this->addFlash('danger', 'Invalid security token.');
        }

        return $this->render('_flashes.html.twig');
    }
}
