<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProjectAddType;
use App\Form\ProjectEditType;
use App\Repository\TaskRepository;

class ProjectController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/project', name: 'app_project')]
    public function index(): Response
    {
        // Fetch only active projects
        $projects = $this->entityManager->getRepository(Project::class)->findBy(['active' => true]);
    
        foreach ($projects as $project) {
            $this->entityManager->refresh($project);
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

    // Create and handle the form
    $form = $this->createForm(ProjectAddType::class, $project);
    $form->handleRequest($request);
    
    
    if ($form->isSubmitted() && $form->isValid()) {
        //dd($form->getData());
        $selectedEmployees = $form->get('employees')->getData();

        // Associate each employee with the project
        foreach ($selectedEmployees as $employee) {
                $project->addEmployee($employee);
                $employee->addProject($project);
        }

        // Persist and save the project with associated employees
        $this->entityManager->persist($project);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
    }

    return $this->render('project/project-add.html.twig', [
        'form' => $form->createView(),
        //'employees' => $employees,
    ]);
}

#[Route('/project/{id}/edit', name: 'project_edit')]
    public function editProject(Request $request, Project $project): Response
    {
        // Create the form and handle the request
        $form = $this->createForm(ProjectEditType::class, $project);
        $form->handleRequest($request);
        $employees = $form->get('employees')->getData();
        foreach ($employees as $employee) {
            $project->addEmployee($employee);
            $employee->addProject($project);
    }

        if ($form->isSubmitted() && $form->isValid()) {
            // Update the employee in the database
            $this->entityManager->flush();
           
            return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
        }

        return $this->render('project/project-edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
            'employees' => $employees
        ]);
    }

    #[Route('/project/{id}', name: 'project_show')]
    public function showProject(int $id, TaskRepository $taskRepository): Response
{
    // Fetch the project by ID
    $project = $this->entityManager->getRepository(Project::class)->find($id);

    // Check if the project was found
    if (!$project) {
        throw $this->createNotFoundException('Project not found');
    }

    // Fetch employees associated with the project
    $employees = $project->getEmployees();
    //dd($employees);
    // Fetch tasks associated with the project
    $tasks = $taskRepository->findBy(['projects' => $project]);

    // Initialize task grouping by status
    $tasksByStatus = [
        'To Do' => [],
        'Doing' => [],
        'Done' => [],
    ];

    foreach ($tasks as $task) {
        $statusLabel = $task->getStatus()->getLabel(); 

        // Ensure the status exists in the grouping
        if (array_key_exists($statusLabel, $tasksByStatus)) {
            $tasksByStatus[$statusLabel][] = $task;
        }
    }
    

    return $this->render('project/project.html.twig', [
        'project' => $project,
        'employees' => $employees,
        'tasksByStatus' => $tasksByStatus, 
        //'tasks' => $tasks
    ]);
}

    #[Route('/project/{id}/delete', name: 'project_delete')]
    public function deleteProject(int $id): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);

    if (!$project) {
        return $this->redirectToRoute('app_project');
    }

    // Set the project as inactive instead of deleting
    $project->setActive(false);
    $this->entityManager->flush();

    // Redirect to the project list after archiving
    return $this->redirectToRoute('app_project');
    }
}
