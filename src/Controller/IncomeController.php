<?php
namespace App\Controller;

use App\Entity\Transaction;
use App\Event\IncomeBalanceEvent;
use App\Form\TransactionTypeForm;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IncomeController extends AbstractController
{
    #[Route('/dashboard/incomes', 'app_income')]
    public function income(TransactionRepository $transactionRepository, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $search = $request->query->get('q');

        if ($search) {
            $queryBuilder = $transactionRepository->search($search, $user, 'income');
        } else {
            $queryBuilder = $transactionRepository->createQueryBuilder('i')
                ->where('i.user= :user')
                ->andWhere('i.type = :type')
                ->setParameter('type', 'income')
                ->setParameter('user', $user);
        }

        $adapter    = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(7);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        return $this->render('dashboard/income.html.twig', [
            'incomes' => $pagerfanta,
        ]);
    }

    #[Route('/dashboard/income/add', 'app_add_income')]
    public function addIncome(Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $income = new Transaction();

        $form = $this->createForm(TransactionTypeForm::class, $income, [
            'user' => $this->getUser(),
            'type' => 'income',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $income->setUser($this->getUser());
            $income->setType('income');
            $accountBalance = $income->getAccount()->getBalance();
            $income->getAccount()->setBalance($accountBalance + $income->getAmount());

            $entityManager->persist($income);
            $entityManager->flush();

            $this->addFlash('success', 'Income added successfully');
            return $this->redirectToRoute('app_income');
        }

        return $this->render('dashboard/transaction-form.html.twig', [
            'form'      => $form->createView(),
            'type'      => 'income',
            'formTitle' => 'Add Income',
        ]);
    }

    #[Route('/dashboard/income/edit/{id}', name: 'app_edit_income')]
    public function editIncome(int $id, Request $request, TransactionRepository $transactionRepository, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher) 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $income = $transactionRepository->find($id);
        $originalAccount = $income->getAccount();

        if (! $income) {
            throw $this->createNotFoundException('Income Not Found');
        }

        if ($income->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Only the creator can edit this income');
        }

        $originalAmount = $income->getAmount();
        
        $form = $this->createForm(TransactionTypeForm::class, $income, [
            'user' => $this->getUser(),
            'type' => 'income',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newAmount  = $income->getAmount();
            $newAccount = $income->getAccount();

            if ($newAccount === $originalAccount) {
                $difference = $newAmount - $originalAmount;
                $newAccount->setBalance($newAccount->getBalance() + $difference); 
            } else {
                $event = new IncomeBalanceEvent($originalAccount, $income, $originalAmount);
                $eventDispatcher->dispatch($event, IncomeBalanceEvent::NAME);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Income updated successfully.');
            return $this->redirectToRoute('app_income');
        }

        return $this->render('dashboard/transaction-form.html.twig', [
            'form'      => $form->createView(),
            'type'      => 'income',
            'formTitle' => 'Edit Income',
        ]);
    }

    #[Route('/dashboard/income/delete/{id}', 'app_delete_income')]
    public function deleteIncome(int $id, TransactionRepository $transactionRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $income = $transactionRepository->find($id);

        $entityManager->remove($income);
        $entityManager->flush();

        $this->addFlash('success', 'Income deleted successfully');
        return $this->redirectToRoute('app_income');
    }
}
