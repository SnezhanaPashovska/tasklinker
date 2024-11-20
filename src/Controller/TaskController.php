<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskAddType;
use App\Form\TaskEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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
    #[IsGranted('ROLE_ADMIN')]
    public function add(Request $request, int $projectId, EntityManagerInterface $entityManager): Response
    {

        $project = $this->entityManager->getRepository(Project::class)->find($projectId);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }
        $tasks = $entityManager->getRepository(Task::class);

        $tasksByStatus = [];
        foreach ($tasks as $task) {
            $status = $task->getStatus();
            if (!isset($tasksByStatus[$status->getId()])) {
                $tasksByStatus[$status->getId()] = ['status' => $status, 'tasks' => []];
            }
            $tasksByStatus[$status->getId()]['tasks'][] = $task;
        }

        $task = new Task();
        $task->setProjects($project);

        $form = $this->createForm(TaskAddType::class, $task, ['project' => $project]);
        $form->handleRequest($request);

        $employees = $entityManager->getRepository(Employee::class);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedEmployees = $form->get('employees')->getData();

            foreach ($selectedEmployees as $employee) {
                $task->addEmployee($employee);
                $employee->addTask($task);
            }

            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $projectId]);
        }

        return $this->render('task/task-add.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
            'employees' => $employees,
            'tasksByStatus' => $tasksByStatus,
        ]);
    }

    #[Route('/task/{id}/edit', name: 'task_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editTask(Request $request, Task $task): Response
    {

        $user = $this->getUser();

        if (!$this->isGranted('ROLE_ADMIN') && $task->getEmployees() !== $user) {
            throw $this->createAccessDeniedException('You do not have permission to edit this task');
        }

        $project = $task->getProjects();

        $form = $this->createForm(TaskEditType::class, $task, [
            'project' => $project,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employees = $form->get('employees')->getData();
            foreach ($employees as $employee) {
                $task->addEmployee($employee);
                $employee->addTask($task);
            }

            $this->entityManager->flush();

            return $this->redirectToRoute('project_show', ['id' => $project->getId()]);
        }

        return $this->render('task/task.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
            'task' => $task,
        ]);
    }

    #[Route('/task/{id}/delete', name: 'task_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteTask(int $id): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return $this->redirectToRoute('task');
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_project');
    }
}
