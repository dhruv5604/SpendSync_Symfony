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
        $user = $security->getUser();

        $expenses = $expenseRepository->findBy(['user'=>$user]);    
        return $this->render('/dashboard/expense.html.twig',[
            "expenses" => $expenses,
        ]);
    }

    #[Route('dashboard/add-expense','app_add_expense')]
    public function addExpense(Request $request, EntityManagerInterface $entityManager, Security $security)
    {
        $expense = new Expense();

        $form = $this->createForm(ExpenseTypeForm::class,$expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $expense->setUser($security->getUser());

            $entityManager->persist($expense);
            $entityManager->flush();

            return $this->redirectToRoute('app_expense');
        }
        
        return $this->render('dashboard/add-expense.html.twig',[
            'form' => $form->createView(),
        ]); 
    }

    #[Route("/dashboard/edit-expense/{id}","app_edit_expense")]
    public function editExpense($id)
    {
        dd($id);
    }
}