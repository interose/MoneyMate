<?php

namespace App\Controller;

use App\Repository\CategoryGroupRepository;
use App\Repository\CategoryRepository;
use App\Repository\SplitTransactionRepository;
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
        #[MapQueryParameter] string $query = null,
    ): Response
    {
        if (null === $month) {
            $month = date('m');
        }

        if (null === $year) {
            $year = date('Y');
        }

        $validSorts = ['valutaDate', 'category'];
        $sort = in_array($sort, $validSorts) ? $sort : 'valutaDate';

        return $this->render('grid/list.html.twig', [
            'transactions' => $repository->findBySearch($month, $year, $sort, $sortDirection, $query),
            'month' => $month,
            'year' => $year,
            'sort' => $sort,
            'sortDirection' => $sortDirection,
            'categoryGroups' => $groupRepository->findBy([], ['name' => 'ASC']),
            'query' => $query
        ]);
    }

    #[Route('/grid/transaction/set-category', name: 'app_grid_transaction_set_category', methods: ['POST'])]
    public function editCategory(TransactionRepository $transactionRepository, SplitTransactionRepository $splitTransactionRepository, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, Request $request): Response
    {
        $transaction = $transactionRepository->findOneById($request->request->getInt('transactionId'));
        $catgory = $categoryRepository->findOneById($request->request->getInt('categoryId'));

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

        if ($request->request->has('splitTransactionId') && 0 !== $request->request->get('splitTransactionId')) {
            $splitTransaction = $splitTransactionRepository->findOneById($request->request->getInt('splitTransactionId'));

            $splitTransaction->setCategory($catgory);
            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Transaction updated!');

            return $this->render('grid/row.stream.html.twig', [
                'transaction' => $transaction,
                'splitTransaction' => $splitTransaction,
            ]);
        } else {
            $transaction->setCategory($catgory);
            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Transaction updated!');

            return $this->render('grid/row.stream.html.twig', [
                'transaction' => $transaction,
            ]);
        }
    }
}
