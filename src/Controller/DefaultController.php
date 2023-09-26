<?php

namespace App\Controller;

use App\Form\QuestionFilterType;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(QuestionRepository $questionRepo, CategoryRepository $categoryRepo, Request $request, PaginatorInterface $paginator): Response
    {
        $categories = $categoryRepo->findAll();
        $form = $this->createForm(QuestionFilterType::class);

        $form->handleRequest($request);

        $queryBuilder = $questionRepo->createQueryBuilder('q');

        $selectedCategories = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedCategories = $form->get('category')->getData();
            $request->getSession()->set('filter_categories', $selectedCategories);
        } elseif ($request->getSession()->has('filter_categories')) {
            $selectedCategories = $request->getSession()->get('filter_categories');
        }

        if ($selectedCategories) {
            $queryBuilder->where('q.category IN (:categories)')
                         ->setParameter('categories', $selectedCategories);
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('default/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
            'filter_form' => $form->createView(),
        ]);
    }
}