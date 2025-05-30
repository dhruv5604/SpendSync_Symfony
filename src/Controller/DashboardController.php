<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Expense;
use App\Entity\Income;
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
        $expenseChart = $this->expenseChart();
        $incomeChart = $this->incomeChart();
        $accountChart = $this->accountChart();
        $totalExpense = $this->totalExpense();
        $totalIncome = $this->totalIncome();
        $totalAccountBalance = $this->totalAccountBalance();
        
        return $this->render('dashboard/dashboard.html.twig',[
            'expenseChart' => $expenseChart,
            'incomeChart' => $incomeChart,
            'accountChart' => $accountChart,
            'totalExpense' => $totalExpense,
            'totalIncome' => $totalIncome,
            'totalAccountBalance' => $totalAccountBalance
        ]);
    }

    public function totalExpense()
    {
        return $this->entityManager->getRepository(Expense::class)
                ->createQueryBuilder('q')
                ->select('SUM(q.Amount) as totalExpense')
                ->andWhere('q.user= :user')
                ->setParameter('user', $this->getUser())
                ->getQuery()
                ->getSingleScalarResult();
    }

    public function totalIncome()
    {
        return $this->entityManager->getRepository(Income::class)
                ->createQueryBuilder('q')
                ->select('SUM(q.amount) as totalIncome')
                ->andWhere('q.user= :user')
                ->setParameter('user', $this->getUser())
                ->getQuery()
                ->getSingleScalarResult();
    }

    public function totalAccountBalance()
    {
        return $this->entityManager->getRepository(Account::class)
                ->createQueryBuilder('q')
                ->select('SUM(q.balance) as totalAmount')
                ->andWhere('q.user= :user')
                ->setParameter('user', $this->getUser())
                ->getQuery()
                ->getSingleScalarResult();
    }

    public function expenseChart()
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $results = $this->entityManager->getRepository(Expense::class)
            ->getExpenseChartDataByCategory($this->getUser());
                
        $labels = [];
        $data = [];
        foreach ($results as $result) {
            $labels[] = $result['Category'];
            $data[] = $result['totalAmount'];
        }

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => ' by CatExpensesegory',
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    'data' => $data,
                ],
            ],
        ]);
        return $chart;
    }

    public function incomeChart()
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $results = $this->entityManager->getRepository(Income::class)
            ->getIncomeChartDataByCategory($this->getUser());
                
        $labels = [];
        $data = [];
        foreach ($results as $result) {
            $labels[] = $result['category'];
            $data[] = $result['total'];
        }

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Expenses by Category',
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    'data' => $data,
                ],
            ],
        ]);
        return $chart;
    }

    public function accountChart()
    {
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $results = $this->entityManager->getRepository(Account::class)
            ->getAccountChartDataByCategory($this->getUser());
        
        $labels = [];
        $data = [];

        foreach ($results as $result) {
            $labels[] = $result['name'];
            $data[] = $result['totalAmount'];
        }

        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Accounts by Category',
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
                    'data' => $data,
                ],
            ],
        ]);
        return $chart;
        
    }

}