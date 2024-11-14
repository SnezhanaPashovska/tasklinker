<?php

namespace App\Controller;

use App\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EmployeeEditType;
use App\Form\EmployeeAddType;
use App\Form\EmployeeConnectType;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class EmployeeController extends AbstractController
{
    
    private EntityManagerInterface $entityManager;
    private EmployeeRepository $employeeRepository;
  
    

    public function __construct(EntityManagerInterface $entityManager, EmployeeRepository $employeeRepository)
    {

        $this->entityManager = $entityManager;
        $this->employeeRepository = $employeeRepository;
        
    }

    #[Route('/employee', name: 'app_employee')]
    
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'EmployeeController',
        ]);
    }

    #[Route('/employee/connect', name: 'connect_employee')]
    
    public function connectEmployee(AuthenticationUtils $authenticationUtils): Response
{
        $error = $authenticationUtils->getLastAuthenticationError();
        $email = $authenticationUtils->getLastUsername();
        var_dump($email);

    // Render the login form with any error messages
    return $this->render('employee/employee-connection.html.twig', [
        'email' => $email,
            'error'         => $error,
    ]);
}

    #[Route('/employees', name: 'employees')]
    #[IsGranted('ROLE_ADMIN')]
    public function listEmployees(): Response
    {
        if (!$this->getUser()) {
            dump("User is not authenticated");
        } else {
            dump($this->getUser()->getRoles());
        }
        $employees = $this->entityManager->getRepository(Employee::class)->findAll();

        return $this->render('employee/employees.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/employee/{id}/edit', name: 'employee_edit')]
   

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

    #[Route('/employee/{id}/delete', name: 'employee_delete')]
    
    public function deleteEmployee(int $id): Response
    {
        $employee = $this->entityManager->getRepository(Employee::class)->find($id);

        if (!$employee) {
            return $this->redirectToRoute('employees');
        }

        // Remove the employee entity
        $this->entityManager->remove($employee);
        $this->entityManager->flush();

        // Redirect to the employee list after successful deletion
        return $this->redirectToRoute('employees');
    }

    #[Route('/employee/add', name: 'add_employee')]
    
    public function addEmployee(Request $request, UserPasswordHasherInterface $hasher): Response
    {

        $employee = new Employee();
        $employee
            ->setContract('CDI')
            ->setArrivalDate(new \DateTimeImmutable());
            
        $form = $this->createForm(EmployeeAddType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $employee->setPassword($hasher->hashPassword($employee, $employee->getPassword()));

            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_project');
        }

        return $this->render('employee/employee-add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
}
