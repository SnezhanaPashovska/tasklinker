<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectAddType;
use App\Form\ProjectEditType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProjectController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/project', name: 'app_project')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        // Get the currently logged-in user (employee)
        $employee = $this->getUser();

        // Check if the user has the 'ROLE_ADMIN' role
        if ($this->isGranted('ROLE_ADMIN')) {
            // Fetch all active projects if the user is an admin
            $projects = $this->entityManager->getRepository(Project::class)->findBy(['active' => true]);
        } else {
            // Fetch only the active projects assigned to the logged-in employee
            $projects = $employee->getProjects()->filter(function ($project) {
                return $project->isActive() === true; // Only show active projects
            });
        }

        return $this->render('homepage/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/add', name: 'add_project')]
    public function add(Request $request): Response
    {

        $project = new Project();
        $project->setActive(true);
        $form = $this->createForm(ProjectAddType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedEmployees = $form->get('employees')->getData();

            foreach ($selectedEmployees as $employee) {
                $project->addEmployee($employee);
                $employee->addProject($project);
            }

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }

        return $this->render('project/project-add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/project/{id}/edit', name: 'project_edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function editProject(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectEditType::class, $project);
        $form->handleRequest($request);
        $employees = $form->get('employees')->getData();
        foreach ($employees as $employee) {
            $project->addEmployee($employee);
            $employee->addProject($project);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }

        return $this->render('project/project-edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
            'employees' => $employees,
        ]);
    }

    #[Route('/project/{id}', name: 'project_show')]
    #[IsGranted('acces_project', 'id')]

    public function showProject(int $id, TaskRepository $taskRepository): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $user = $this->getUser();
        $employees = $project->getEmployees();
        $tasks = $taskRepository->findBy(['projects' => $project]);

        if (!$this->isGranted('ROLE_ADMIN')) {
            $tasks = array_filter($tasks, function ($task) use ($user) {
                return $task->getEmployees() && $task->getEmployees()->getId() === $user->getId();
            });
        }

        $tasksByStatus = [
            'To Do' => [],
            'Doing' => [],
            'Done' => [],
        ];

        foreach ($tasks as $task) {
            $statusLabel = $task->getStatus() ? $task->getStatus()->getLabel() : 'Unknown';

            if (array_key_exists($statusLabel, $tasksByStatus)) {
                $tasksByStatus[$statusLabel][] = $task;
            }
        }

        return $this->render('project/project.html.twig', [
            'project' => $project,
            'employees' => $employees,
            'tasksByStatus' => $tasksByStatus,
        ]);
    }

    #[Route('/project/{id}/delete', name: 'project_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteProject(int $id): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

        if (!$project) {
            return $this->redirectToRoute('app_project');
        }

        $project->setActive(false);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_project');
    }
}
