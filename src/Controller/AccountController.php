<?php

namespace App\Controller;

use App\Entity\Account;
use App\Lib\FinTs\FinTsSymfonyAdapter;
use Fhp\CurlException;
use Fhp\Protocol\ServerException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account')]
class AccountController extends AbstractController
{
//    #[Route('/{id}/update-statements', name: 'app_account_update_statements')]
//    public function updateAccountStatements(
//        SubAccount $subAccount,
//        Wrapper $finTsWrapper,
//        Request $request,
//        ImportHandler $importHandler,
//        CurrentBalanceRepository $currentBalanceRepository,
//    ): Response {
//        try {
//            $to = new \DateTime();
//            $from = new \DateTime();
//            $from->modify(sprintf('- %d days', 90));
//
//            $transactions = $finTsWrapper->getStatements(
//                $subAccount->getAccount(),
//                $subAccount->getSEPAAcount(),
//                $from,
//                $to,
//                $request->query->get('action')
//            );
//
//////            $currentBalance = $finTsWrapper->getBalance(
//////                $subAccount->getAccount(),
//////                $subAccount->getSEPAAcount(),
//////            );
//        } catch (TanRequiredException $e) {
//            return $this->render('account/confirmTanMedia.html.twig', [
//                'action' => Action::CheckDecoupled->value,
//                'subAccount' => $subAccount,
//                'message' => $e->getMessage(),
//            ]);
//        } catch (\Exception $e) {
//            $this->addFlash('error_static', $e->getMessage());
//
//            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//////        $currentBalanceRepository->updateBalance($subAccount, $currentBalance);
//
//        if (0 === count($transactions)) {
//            $this->addFlash('error_static', 'No transactions found.');
//
//            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        $stats = $importHandler->import($transactions, $subAccount);
//        $this->addFlash('success_static', sprintf('Overall = %d<br>New = %d<br>Assigned = %d', $stats->iTransactions, $stats->iNew, $stats->iAssigned));
//
//        return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
//    }

    #[Route('/tan-poll/{id}', name: 'app_account_tan_poll', methods: ['POST', 'GET'])]
    public function tanPoll(Account $account, FinTsSymfonyAdapter $finTsAdapter): Response
    {
        try {
            $resolved = $finTsAdapter->checkPending($account);
        } catch (\LogicException) {
            return new Response(status: 200);
        } catch (CurlException $e) {
        } catch (ServerException $e) {
        }

        return new Response(status: $resolved ? 200 : 202);
    }
}
