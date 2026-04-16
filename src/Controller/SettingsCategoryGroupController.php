<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\CategoryGroup;
use App\Form\CategoryGroupType;
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

#[Route('/settings/category-group')]
class SettingsCategoryGroupController extends AbstractController
{
    #[Route('/', name: 'app_settings_categorygroup_index', methods: ['GET'])]
    public function indexUnified(
        CategoryGroupRepository $groupRepository,
        CategoryRepository $categoryRepository,
    ): Response {
        $groups = $groupRepository->findBy([], ['name' => 'ASC']);
        $countUngrouped = $categoryRepository->count(['categoryGroup' => null]);

        return $this->render('settings_category_group/index.html.twig', [
            'groups' => $groups,
            'countUngrouped' => $countUngrouped
        ]);
    }

    #[Route('/new', name: 'app_settings_categorygroup_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categoryGroup = new CategoryGroup();
        $form = $this->createForm(CategoryGroupType::class, $categoryGroup, [
            'action' => $this->generateUrl('app_settings_categorygroup_new'),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categoryGroup);
            $entityManager->flush();

            $this->addFlash('success', 'Category Group created');

            return $this->redirectToRoute('app_settings_categorygroup_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('settings_category_group/new.html.twig', [
            'categoryGroup' => $categoryGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/update', name: 'app_settings_categorygroup_update', methods: ['POST'])]
    public function update(Request $request, CategoryGroup $group, EntityManagerInterface $em): Response
    {
        $name = $request->request->get('groupname');
        $color = $request->request->get('groupcolor');

        $group->setName($name);
        $group->setColor($color);
        $em->flush();

        $this->addFlash('success', 'Category Group updated');

        if ($request->getPreferredFormat() === TurboBundle::STREAM_FORMAT) {
            return $this->render('settings_category_group/editBar.stream.twig', [
                'group' => $group
            ], new Response('', 200, ['Content-Type' => 'text/vnd.turbo-stream.html']));
        }

        return $this->redirectToRoute('app_settings_categorygroup_index');
    }

    #[Route('/{id}', name: 'app_settings_categorygroup_delete', methods: ['POST'])]
    public function delete(Request $request, CategoryGroup $group, EntityManagerInterface $entityManager): Response
    {
        $isValidToken = $this->isCsrfTokenValid('group-edit'.$group->getId(), $request->getPayload()->getString('_token'));

        if ($isValidToken) {
            if ($group->getCategories()->count() > 0) {
                $this->addFlash('error', 'Category Group is not empty!');
            } else {
                $entityManager->remove($group);
                $entityManager->flush();

                $this->addFlash('success', 'Category Group deleted!');

                return $this->redirectToRoute('app_settings_categorygroup_index');
            }
        } else {
            $this->addFlash('danger', 'Invalid security token.');
        }

        return $this->render('_flashes.html.twig', [],
            new Response('', 200, ['Content-Type' => 'text/vnd.turbo-stream.html'])
        );
    }
}
