<?php

namespace App\Controller;

use App\Lib\Manager\SettingsManager;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(TransactionRepository $repository, SettingsManager $settingsManager): Response
    {
        return $this->redirectToRoute('app_transaction_index');

//        $mainAccount = $settingsManager->get(SettingsManager::SETTING_MAIN_ACCOUNT);
//        if ($mainAccount) {
//
//            $start = new \DateTime();
//            $end = clone $start;
//            $start->setTime(0, 0, 0);
//            $start->modify('- 13 months');
//            $end->setTime(23, 59, 59);
//
//            $turnover = $repository->getTurnoverByMonth($mainAccount, $start, $end);
//            $chartData = [];
//            $categories = [];
//
//            array_walk($turnover, function ($item) use (&$chartData, &$categories) {
//                $categories[] = $item['month_name'];
//                $chartData['debit'][] = $item['debit'] / 100;
//                $chartData['credit'][] = $item['credit'] / 100;
//            });
//
//            $turnover = [
//                'categories' => $categories,
//                'chartData' => $chartData,
//            ];
//        } else {
//            $turnover = [];
//            $this->addFlash('error_static', 'Please set a main account in the settings.');
//        }
//
//        return $this->render('dashboard/index.html.twig', [
//            'turnoverChartData' => json_encode($turnover),
//        ]);
    }
}
