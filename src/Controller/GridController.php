<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Repository\CategoryGroupRepository;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

class GridController extends AbstractController
{
    #[Route('/grid/monthly', name: 'app_grid_monthly', methods: ['GET'])]
    public function index(
        TransactionRepository $repository,
        CategoryGroupRepository $groupRepository,
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

        return $this->render('grid/list.html.twig', [
            'transactions' => $repository->findBySearch($month, $year, $sort, $sortDirection),
            'month' => $month,
            'year' => $year,
            'sort' => $sort,
            'sortDirection' => $sortDirection,
            'categoryGroups' => $groupRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/grid/transaction/set-category', name: 'app_grid_transaction_set_category', methods: ['POST'])]
    public function editCategory(TransactionRepository $transactionRepository, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, Request $request): Response
    {
        $transaction = $transactionRepository->findOneById($request->get('transactionId'));
        $catgory = $categoryRepository->findOneById($request->get('categoryId'));

        $transaction->setCategory($catgory);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $this->addFlash('success', 'Transaction updated!');

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

        return $this->render('grid/row.stream.html.twig', ['transaction' => $transaction]);
    }
}
