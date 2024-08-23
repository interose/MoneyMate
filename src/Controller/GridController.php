<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class GridController extends AbstractController
{
    #[Route('/grid/monthly', name: 'app_grid_monthly', methods: ['GET'])]
    public function index(
        TransactionRepository $repository,
        #[MapQueryParameter] int $month = null,
        #[MapQueryParameter] int $year = null,
        #[MapQueryParameter] string $sort = 'valutaDate',
        #[MapQueryParameter] string $sortDirection = 'ASC',
    ): Response
    {
        if ($month === null) {
            $month = date('m');
        }

        if ($year === null) {
            $year = date('Y');
        }

        $validSorts = ['valutaDate', 'category'];
        $sort = in_array($sort, $validSorts) ? $sort : 'valutaDate';

        return $this->render('grid/monthly.html.twig', [
            'transactions' => $repository->findBySearch($month, $year, $sort, $sortDirection),
            'month' => $month,
            'year' => $year,
            'sort' => $sort,
            'sortDirection' => $sortDirection,
        ]);
    }
}
