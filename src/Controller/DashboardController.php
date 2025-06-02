<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class DashboardController extends AbstractController
{   
    private ChartBuilderInterface $chartBuilder;
    private EntityManagerInterface $entityManager;
    public function __construct(ChartBuilderInterface $chartBuilder, EntityManagerInterface $entityManager)
    {
        $this->chartBuilder = $chartBuilder;
        $this->entityManager = $entityManager;
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard()
    {
        $accountChart = $this->accountChart();
        $expenseChart = $this->getChart('expense');
        $incomeChart = $this->getChart('income');
        $totalExpense = $this->getRepository(Transaction::class)
            ->getTotalAmountByType('expense', $this->getUser());
        $totalIncome = $this->getRepository(Transaction::class)
            ->getTotalAmountByType('income', $this->getUser());
        $totalAccountBalance = $this->getRepository(Account::class)->getTotalAccountBalance($this->getUser());
        $recentTransactions = $this->getRepository(Transaction::class)->getRecentTransactions($this->getUser());
        
        return $this->render('dashboard/dashboard.html.twig',[
            'expenseChart' => $expenseChart,
            'incomeChart' => $incomeChart,
            'accountChart' => $accountChart,
            'totalExpense' => $totalExpense,
            'totalIncome' => $totalIncome,
            'totalAccountBalance' => $totalAccountBalance,
            'recentTransactions' => $recentTransactions,
        ]);
    }

    public function getChart(string $type)
    {
        $results = $this->getRepository(Transaction::class)
            ->getChartDataByCategory($this->getUser(), $type);
        
        return $this->prepareDoughnutChart($results, $type . ' by category');
    }

    public function accountChart()
    {
        $results = $this->getRepository(Account::class)
            ->getAccountChartDataByCategory($this->getUser());

        return $this->prepareDoughnutChart($results, 'Account');
    }

    private function prepareDoughnutChart(array $results, string $label)
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);

        $labels = array_column($results, array_key_exists('category', $results[0]) ? 'category' : 'name');
        $data = array_column($results, 'totalAmount');

        $chart->setData([
            'labels' => $labels,
            'datasets' => [[
                'label' => $label,
                'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                'data' => $data,    
            ]]
        ]);

        return $chart;
    }
    
    public function getRepository($class){
        return $this->entityManager->getRepository($class);
    }
}