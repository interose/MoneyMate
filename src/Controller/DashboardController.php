<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->getCategoriesForDropdown();

        return $this->render('dashboard/index.html.twig', [
            'categories' => $categories
        ]);
    }
}
