<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ProjectAddType;
use App\Form\ProjectEditType;

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
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

    return $this->render('homepage/index.html.twig', [
        'projects' => $projects,
    ]);
    }

    #[Route('/project/add', name: 'add_project')]
    public function add(Request $request): Response
    {

    $project = new Project();
    

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
    public function showProject(int $id): Response
    {
        $project = $this->entityManager->getRepository(Project::class)->find($id);
        $employees = $project->getEmployees();

   

    return $this->render('project/project.html.twig', [
        'project' => $project,
        'employees' => $employees,
    ]);
    }
}
