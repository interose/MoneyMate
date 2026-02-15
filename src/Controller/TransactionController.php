<?php

namespace App\Controller;

use App\Entity\SplitTransaction;
use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Lib\Manager\SettingsManager;
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

class TransactionController extends AbstractController
{
    #[Route('/transaction', name: 'app_transaction_index', methods: ['GET'])]
    public function index(
        TransactionRepository $repository,
        CategoryRepository $categoryRepository,
        SettingsManager $settingsManager,
        #[MapQueryParameter] ?int $month = null,
        #[MapQueryParameter] ?int $year = null,
        #[MapQueryParameter] string $sort = 'valutaDate',
        #[MapQueryParameter] string $sortDirection = 'asc',
        #[MapQueryParameter] ?string $query = null,
    ): Response {
        if (null === $month) {
            $month = date('m');
        }

        if (null === $year) {
            $year = date('Y');
        }

        $validSorts = ['valutaDate', 'category'];
        $sort = in_array($sort, $validSorts) ? $sort : 'valutaDate';

        $mainAccount = $settingsManager->get(SettingsManager::SETTING_MAIN_ACCOUNT);
        if ($mainAccount) {
            $transactions = $repository->findBySearch($month, $year, $sort, $sortDirection, $query);
        } else {
            $transactions = [];
            $this->addFlash('error_static', 'Please set a main account in the settings.');
        }

        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactions,
            'month' => $month,
            'year' => $year,
            'sort' => $sort,
            'sortDirection' => $sortDirection,
            'categoryGroups' => $categoryRepository->getCategoriesForDropdown(),
            'query' => $query,
        ]);
    }

    #[Route('/transaction/set-category', name: 'app_transaction_set_category', methods: ['POST'])]
    public function editCategory(TransactionRepository $transactionRepository, SplitTransactionRepository $splitTransactionRepository, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, Request $request): Response
    {
        $transaction = $transactionRepository->findOneById($request->request->getInt('transactionId'));
        $catgory = $categoryRepository->findOneById($request->request->getInt('categoryId'));

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);

        if ($request->request->has('splitTransactionId') && 0 !== $request->request->getInt('splitTransactionId')) {
            $splitTransaction = $splitTransactionRepository->findOneById($request->request->getInt('splitTransactionId'));

            $splitTransaction->setCategory($catgory);
            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Transaction updated!');

            return $this->render('transaction/row.stream.html.twig', [
                'transaction' => $transaction,
                'splitTransaction' => $splitTransaction,
            ]);
        } else {
            $transaction->setCategory($catgory);
            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Transaction updated!');

            return $this->render('transaction/row.stream.html.twig', [
                'transaction' => $transaction,
            ]);
        }
    }

    #[Route('/transaction/{id}/update', name: 'app_transaction_update', methods: ['GET', 'POST'])]
    public function saveSplit(Transaction $transaction, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$transaction->hasSplitTransactions()) {
            $transaction->addSplitTransaction(new SplitTransaction());
        }

        $form = $this->createForm(TransactionType::class, $transaction, [
            'action' => $this->generateUrl('app_transaction_update', ['id' => $transaction->getId()]),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Successful updated!');

            return $this->redirectToRoute('app_transaction_monthly', [
                'year' => $transaction->getValutaDate()->format('Y'),
                'month' => $transaction->getValutaDate()->format('n'),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction/splitTransaction.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }
}
