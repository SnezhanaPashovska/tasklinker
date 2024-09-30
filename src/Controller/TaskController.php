<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\TaskAddType;
use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/task', name: 'app_task')]
    public function index(): Response
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findAll();
        
        return $this->render('task/task.html.twig', [
            'controller_name' => 'TaskController',
            'tasks' => $tasks,
        ]);
    }

    #[Route('/task/add/{projectId}', name: 'task_add')]
    public function add(Request $request, int $projectId, EntityManagerInterface $entityManager): Response
    {

        $project = $this->entityManager->getRepository(Project::class)->find($projectId);

    if (!$project) {
        throw $this->createNotFoundException('Project not found');
    }

    $task = new Task();
    $task->setProjects($project);

    // Create and handle the form
    $form = $this->createForm(TaskAddType::class, $task, ['project' => $project]);
    $form->handleRequest($request);
    
    
    if ($form->isSubmitted() && $form->isValid()) {
        //dd($form->getData());
        $selectedEmployees = $form->get('employees')->getData();

        // Associate each employee with the task
        foreach ($selectedEmployees as $employee) {
                $task->addEmployee($employee);
                $employee->addTask($task);
        }

        // Persist and save the task with associated employees
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('task_add', ['projectId' => $projectId]);
    }

    return $this->render('task/task-add.html.twig', [
        'form' => $form->createView(),
       'project' => $project,
    ]);
}
}
