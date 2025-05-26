<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Form\ExpenseTypeForm;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ExpenseController extends AbstractController
{
    #[Route('dashboard/expense',"app_expense")]
    public function expense(ExpenseRepository $expenseRepository,Security $security)
    {   
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();

        $expenses = $expenseRepository->findBy(['user'=>$user]);    
        return $this->render('/dashboard/expense.html.twig',[
            "expenses" => $expenses,
        ]);
    }

    #[Route('dashboard/expense/add','app_add_expense')]
    public function addExpense(Request $request, EntityManagerInterface $entityManager, Security $security)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $expense = new Expense();

        $form = $this->createForm(ExpenseTypeForm::class,$expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $expense->setUser($security->getUser());

            $entityManager->persist($expense);
            $entityManager->flush();
            
            $this->addFlash('success','Expense added successfully');

            return $this->redirectToRoute('app_expense');
        }
        
        return $this->render('dashboard/add-expense.html.twig',[
            'form' => $form->createView(),
        ]); 
    }

    #[Route("/dashboard/expense/edit/{id}","app_edit_expense")]
    public function editExpense(int $id,Request $request, EntityManagerInterface $entityManager, ExpenseRepository $expenseRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $expense = $expenseRepository->find($id);

        if (!$expense) {
            throw $this->createNotFoundException('Expense Not Found');
        }

        if ($expense->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Only Creator of this expense will edit this expense');
        }

        $form = $this->createForm(ExpenseTypeForm::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success','Expense updated Successfully');
            return $this->redirectToRoute('app_expense');
        }

        return $this->render('dashboard/add-expense.html.twig',[
            "form" => $form->createView()
        ]);
    }

    #[Route('dashboard/expense/delete/{id}','app_delete_expense')]
    public function deleteExpense(int $id, ExpenseRepository $expenseRepository, EntityManagerInterface $entityManager)
    {
        $expense = $expenseRepository->find($id);

        $entityManager->remove($expense);
        $entityManager->flush();

        $this->addFlash('success','Expense deleted successfully');
        return $this->redirectToRoute('app_expense');
    }
}