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
        $lastUpdate = $updater->getLastUpdated();

        $validSorts = ['name', 'industry', 'geoPak10', 'profitConsistency', 'lossRatio', 'dividendYield', 'sharePrice', 'gd200', 'trend', 'comment'];
        $sort = in_array($sort, $validSorts) ? $sort : 'name';

        return $this->render('stock/index.html.twig', [
            'champions' => $repository->findBySearchAndFilter($sort, $sortDirection, $query, $filter),
            'sort' => $sort,
            'sortDirection' => $sortDirection,
            'trends' => StockTrend::cases(),
            'comments' => StockComment::cases(),
            'lastUpdate' => $lastUpdate ? $lastUpdate->format('d.m.Y H:i') : 'not updated yet',
        ]);
    }

    #[Route('/update', name: 'app_stock_update', methods: ['GET'])]
    public function update(ChampionUpdater $updater): Response
    {
        try {
            if ($updater->getUpdate()) {
                $this->addFlash('success', 'Champions updated.');
            } else {
                $this->addFlash('notice', 'Nothing to do.');
            }
        } catch (\Exception $e) {
            $this->addFlash('error_static', $e->getMessage());
        }

        return $this->redirectToRoute('app_stock_index', [], Response::HTTP_SEE_OTHER);
    }
}
