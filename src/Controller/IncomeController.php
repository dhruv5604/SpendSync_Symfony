<?php

namespace App\Controller;

use App\Entity\Income;
use App\Form\IncomeTypeForm;
use App\Repository\IncomeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IncomeController extends AbstractController
{
    #[Route('/dashboard/income','app_income')]
    public function income(IncomeRepository $incomeRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $incomes = $incomeRepository->findBy(['user'=>$this->getUser()]);

        return $this->render('dashboard/income.html.twig',[
            'incomes' => $incomes
        ]);
    }

    #[Route('dashboard/income/add','app_add_income')]
    public function addIncome(Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $income = new Income();

        $form = $this->createForm(IncomeTypeForm::class, $income);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $income->setUser($this->getUser());

            $entityManager->persist($income);
            $entityManager->flush();

            $this->addFlash('success','Income added successfully');
            return $this->redirectToRoute('app_income');
        }

        return $this->render('dashboard/add-income.html.twig',[
            'form' => $form->createView()
        ]);
    }

    #[Route('dashboard/income/edit/{id}','app_edit_income')]
    public function editIncome(int $id, Request $request, IncomeRepository $incomeRepository, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $income = $incomeRepository->find($id);

        if (!$income) {
            throw $this->createNotFoundException('Expense Not Found');
        }

        if ($income->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Only Creator of this Income will edit this Income');
        }

        $form = $this->createForm(IncomeTypeForm::class, $income);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success','Income updated Successfully');
            return $this->redirectToRoute('app_income');
        }

        return $this->render('dashboard/add-income.html.twig',[
            "form" => $form->createView()
        ]);
    }

    #[Route('dashboard/income/delete/{id}','app_delete_income')]
    public function deleteIncome(int $id, IncomeRepository $incomeRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $income = $incomeRepository->find($id);

        $entityManager->remove($income);
        $entityManager->flush();

        $this->addFlash('success','Income deleted successfully');
        return $this->redirectToRoute('app_income');
    }
}