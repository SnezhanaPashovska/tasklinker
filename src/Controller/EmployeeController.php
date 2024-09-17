<?php

namespace App\Controller;

use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EmployeeEditType;
use Symfony\Component\HttpFoundation\Request;

class EmployeeController extends AbstractController
{

    private EntityManagerInterface $entityManager;

    // Injecting EntityManagerInterface into the controller
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/employee', name: 'app_employee')]
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'EmployeeController',
        ]);
    }

    #[Route('/employees', name: 'employees')]
    public function listEmployees(): Response
    {
        
        $employees = $this->entityManager->getRepository(Employee::class)->findAll();

      
        return $this->render('employee/employees.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/employee/{id}', name: 'employee_edit')]
    public function editEmployee(Request $request, Employee $employee): Response
    {
        // Create the form and handle the request
        $form = $this->createForm(EmployeeEditType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Update the employee in the database
            $this->entityManager->flush();
           

            return $this->redirectToRoute('employees');
        }

        return $this->render('employee/employee.html.twig', [
            'form' => $form->createView(),
            'employee' => $employee
        ]);
    }
}
