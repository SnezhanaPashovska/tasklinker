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

    #[Route('/project/add', name: 'add_project', methods: ['POST'])]
    public function add(Request $request): Response
    {

    $project = new Project();
    $employees = $this->entityManager->getRepository(Employee::class)->findAll();

    // Create and handle the form
    $form = $this->createForm(ProjectAddType::class, $project);
    $form->handleRequest($request);
    dd($form->getData());
    
    if ($form->isSubmitted() && $form->isValid()) {
       
        $selectedEmployees = $form->get('employees')->getData();

        // Associate each employee with the project
        foreach ($selectedEmployees as $employee) {
            $project->addEmployee($employee);
        }

        // Persist and save the project with associated employees
        $this->entityManager->persist($project);
        $this->entityManager->flush();

        

        return $this->redirectToRoute('app_project', ['id' => $project->getId()]);
    }

    return $this->render('project/project-add.html.twig', [
        'employees' => $employees,
        'form' => $form->createView(),
    ]);
}
}
