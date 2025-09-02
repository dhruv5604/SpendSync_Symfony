<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountTypeForm;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/dashboard/accounts','app_account')]
    public function account(Request $request, AccountRepository $accountRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $search = $request->query->get('q');

        if ($search) {
            $queryBuilder = $accountRepository->search($search, $user);
        } else {
            $queryBuilder = $accountRepository->createQueryBuilder('e')
                ->where('e.user = :user')
                ->setParameter('user', $user);
        }
        $adapter = new QueryAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(7);
        $pagerfanta->setCurrentPage($request->query->getInt('page', 1));

        return $this->render('/dashboard/account.html.twig', [
            "accounts" => $pagerfanta,
        ]);
    }

    #[Route('/dashboard/accounts/add', 'app_add_account')]
    public function addAccount(Request $request, AccountRepository $accountRepository, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $account = new Account();

        $form = $this->createForm(AccountTypeForm::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $account->setUser($this->getUser());

            $entityManager->persist($account);
            $entityManager->flush();

            $this->addFlash('success', 'Account added successfully');

            return $this->redirectToRoute('app_account');
        }

        return $this->render('dashboard/account-form.html.twig', [
            'form' => $form->createView(),
            'formTitle' => 'Add Account',
        ]);
    }

    #[Route("/dashboard/account/edit/{id}", "app_edit_account")]
    public function editAccount(int $id, Request $request, EntityManagerInterface $entityManager, AccountRepository $accountRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $account = $accountRepository->find($id);

        if (!$account) {
            throw $this->createNotFoundException('Account Not Found');
        }

        if ($account->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Only Creator of this account will edit this account');
        }

        $form = $this->createForm(AccountTypeForm::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Account updated Successfully');
            return $this->redirectToRoute('app_account');
        }

        return $this->render('dashboard/account-form.html.twig', [
            "form" => $form->createView(),
            'formTitle' => 'Edit Account',
        ]);
    }

    #[Route('/dashboard/account/delete/{id}', 'app_delete_account')]
    public function deleteAccount(int $id, AccountRepository $accountRepository, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $account = $accountRepository->find($id);

        if (!$account) {
            throw $this->createNotFoundException('Account Not Found');
        }

        if ($account->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Only Creator of this account can delete this account');
        }

        $entityManager->remove($account);
        $entityManager->flush();

        $this->addFlash('success', 'Account deleted successfully');
        return $this->redirectToRoute('app_account');
    }

    #[Route('/dashboard/account/chart', 'app_account_chart')]
    public function getAccountChartDataByCategory(AccountRepository $accountRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $results = $accountRepository->getAccountChartDataByCategory($user);
        
        $labels = [];
        $data = [];

        foreach ($results as $result) {
            $labels[] = $result['name'];
            $data[] = $result['totalAmount'];
        }

        return new JsonResponse([
            'labels' => $labels,
            'data' => $data,
        ]);
    }
}