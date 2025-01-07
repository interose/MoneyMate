<?php

namespace App\Controller;

use App\Config\StockComment;
use App\Config\StockTrend;
use App\Lib\Stock\ChampionUpdater;
use App\Repository\StockChampionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stock')]
class StockController extends AbstractController
{
    #[Route('/', name: 'app_stock_index', methods: ['GET'])]
    public function index(
        StockChampionRepository $repository,
        ChampionUpdater $updater,
        #[MapQueryParameter] string $sort = 'name',
        #[MapQueryParameter] string $sortDirection = 'asc',
        #[MapQueryParameter] string $query = '',
        #[MapQueryParameter] ?array $filter = null,
    ): Response {
        try {
            $updater->checkUpdate();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        $validSorts = ['name', 'industry', 'geoPak10', 'profitConsistency', 'lossRatio', 'dividendYield', 'sharePrice', 'gd200', 'trend', 'comment'];
        $sort = in_array($sort, $validSorts) ? $sort : 'name';

        return $this->render('stock/index.html.twig', [
            'champions' => $repository->findBySearchAndFilter($sort, $sortDirection, $query, $filter),
            'sort' => $sort,
            'sortDirection' => $sortDirection,
            'trends' => StockTrend::cases(),
            'comments' => StockComment::cases(),
        ]);
    }
}