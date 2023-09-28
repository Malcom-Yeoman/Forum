<?php

namespace App\Controller;

use App\Entity\Question;
use App\Form\QuestionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    #[Route('/question/ajouter', name: 'app_question_add')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionType::class, $question);
        
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {           
            $question->setDatePosted(new \DateTime());
            $question->setUser($this->getUser()); 
            $question->setIsAnswered(false);
            $entityManager->persist($question);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_question_view', ['id' => $question->getId()]);
        }
    
        return $this->render('question/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}