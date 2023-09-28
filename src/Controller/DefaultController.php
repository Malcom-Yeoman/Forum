<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Form\QuestionFilterType;
use App\Form\AnswerType;
use App\Repository\CategoryRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route(
        path: '/question/{id}', 
        name: 'app_question_view', 
        requirements: ["id" => "\d+"]
    )]    
    public function viewQuestion(int $id, QuestionRepository $questionRepo, Request $request, EntityManagerInterface $entityManager): Response
    {
        $question = $questionRepo->find($id);

        if (!$question) {
            throw $this->createNotFoundException('La question demandée n\'existe pas.');
        }

        $answer = new Answer();
        $form = $this->createForm(AnswerType::class, $answer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setQuestion($question);
            $answer->setUser($this->getUser());
            $answer->setDatePosted(new \DateTime());

            $entityManager->persist($answer);
            $entityManager->flush();

            $this->addFlash('success', 'Votre réponse a été ajoutée avec succès !');

            return $this->redirectToRoute('app_question_view', ['id' => $question->getId()]);
        }

        return $this->render('default/question.html.twig', [
            'question' => $question,
            'form' => $form->createView()
        ]);
    }
}