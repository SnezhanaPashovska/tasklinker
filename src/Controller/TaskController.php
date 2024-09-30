<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->findAll();
        
        return $this->render('task/task.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
}
