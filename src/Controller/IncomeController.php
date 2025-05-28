<?php
namespace App\Controller;

use App\Entity\Income;
use App\Form\IncomeTypeForm;
use App\Repository\IncomeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IncomeController extends AbstractController
{
    #[Route('/dashboard/income', 'app_income')]
    public function income(IncomeRepository $incomeRepository, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $search = $request->query->get('q');

        if ($search) {
            $queryBuilder = $incomeRepository->search($search, $user);
        } else {
            $queryBuilder = $incomeRepository->createQueryBuilder('i')
                ->where('i.user= :user')
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

    #[Route('dashboard/income/add', 'app_add_income')]
    public function addIncome(Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $income = new Income();

        $form = $this->createForm(IncomeTypeForm::class, $income, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $income->setUser($this->getUser());

            $accountBalance = $income->getAccount()->getBalance();
            $income->getAccount()->setBalance($accountBalance + $income->getAmount());

            $entityManager->persist($income);
            $entityManager->flush();

            $this->addFlash('success', 'Income added successfully');
            return $this->redirectToRoute('app_income');
        }

        return $this->render('dashboard/income-form.html.twig', [
            'form'      => $form->createView(),
            'formTitle' => 'Add Income',
        ]);
    }

    #[Route('dashboard/income/edit/{id}', name: 'app_edit_income')]
    public function editIncome(int $id, Request $request, IncomeRepository $incomeRepository, EntityManagerInterface $entityManager) 
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $income = $incomeRepository->find($id);

        if (! $income) {
            throw $this->createNotFoundException('Income Not Found');
        }

        if ($income->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Only the creator can edit this income');
        }

        $originalAmount = $income->getAmount();
        
        $form = $this->createForm(IncomeTypeForm::class, $income, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newAmount  = $income->getAmount();
            $difference = $newAmount - $originalAmount;

            $account = $income->getAccount();
            $account->setBalance($account->getBalance() + $difference);

            $entityManager->flush();

            $this->addFlash('success', 'Income updated successfully.');
            return $this->redirectToRoute('app_income');
        }

        return $this->render('dashboard/income-form.html.twig', [
            'form'      => $form->createView(),
            'formTitle' => 'Edit Income',
        ]);
    }

    #[Route('dashboard/income/delete/{id}', 'app_delete_income')]
    public function deleteIncome(int $id, IncomeRepository $incomeRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $income = $incomeRepository->find($id);

        $entityManager->remove($income);
        $entityManager->flush();

        $this->addFlash('success', 'Income deleted successfully');
        return $this->redirectToRoute('app_income');
    }
}
