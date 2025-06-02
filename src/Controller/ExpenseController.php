<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Event\ExpenseBalanceEvent;
use App\Form\TransactionTypeForm;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    #[Route('dashboard/expense', "app_expense")]
    public function expense(Request $request, TransactionRepository $transactionRepository, Security $security)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();

        $search = $request->query->get('q');

        if ($search) {
            $queryBuilder = $transactionRepository->search($search, $user, 'expense');
        } else {
            $queryBuilder = $transactionRepository->createQueryBuilder('e')
                ->andWhere('e.user= :user')
                ->andWhere('e.type = :type')
                ->setParameter('type', 'expense')
                ->setParameter('user', $user);
        }
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(7);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));
        
        return $this->render('/dashboard/expense.html.twig', [
            "expenses" => $pagerfanta,
        ]);
    }

    #[Route('dashboard/expense/add', 'app_add_expense')]
    public function addExpense(Request $request, EntityManagerInterface $entityManager, Security $security)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $transaction = new Transaction();

        $form = $this->createForm(TransactionTypeForm::class, $transaction,[
            'user' => $this->getUser(),
            'type' => 'expense',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $transaction->setUser($security->getUser());
            $transaction->setType('expense');
            $accountBalance = $transaction->getAccount()->getBalance();
            $transaction->getAccount()->setBalance($accountBalance - $transaction->getAmount());

            $entityManager->persist($transaction);
            $entityManager->flush();

            $this->addFlash('success', 'Expense added successfully');

            return $this->redirectToRoute('app_expense');
        }

        return $this->render('dashboard/transaction-form.html.twig', [
            'form' => $form->createView(),
            'type' => 'expense',
            'formTitle' => 'Add Expense',
        ]);
    }

    #[Route("/dashboard/expense/edit/{id}", "app_edit_expense")]
    public function editExpense(int $id, Request $request, EntityManagerInterface $entityManager, TransactionRepository $transactionRepository, EventDispatcherInterface $eventDispatcher)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $expense = $transactionRepository->find($id);
        $originalAccount = $expense->getAccount();

        if (!$expense) {
            throw $this->createNotFoundException('Expense Not Found');
        }

        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Only Creator of this expense will edit this expense');
        }

        $originalAmount = $expense->getAmount();
        
        $form = $this->createForm(TransactionTypeForm::class, $expense, [
            'user' => $this->getUser(),
            'type' => 'expense',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newAmount = $expense->getAmount();
            $difference = $newAmount - $originalAmount;

            $account = $expense->getAccount();
            $account->setBalance($account->getBalance() - $difference);

            $newAccount = $expense->getAccount()->getName();

            if ($newAccount !== $originalAccount->getName()) { 
                $event = new ExpenseBalanceEvent($originalAccount, $expense);
                $eventDispatcher->dispatch($event, ExpenseBalanceEvent::NAME);
            }

            $expense->setUser($this->getUser());
            $entityManager->flush();

            $this->addFlash('success', 'Expense updated Successfully');
            return $this->redirectToRoute('app_expense');
        }

        return $this->render('dashboard/transaction-form.html.twig', [
            "form" => $form->createView(),
            "type" => 'expense',
            "formTitle" => "Edit Expense",
        ]);
    }

    #[Route('dashboard/expense/delete/{id}', 'app_delete_expense')]
    public function deleteExpense(int $id, TransactionRepository $transactionRepository, EntityManagerInterface $entityManager)
    {
        $expense = $transactionRepository->find($id);

        $entityManager->remove($expense);
        $entityManager->flush();

        $this->addFlash('success', 'Expense deleted successfully');
        return $this->redirectToRoute('app_expense');
    }
}
