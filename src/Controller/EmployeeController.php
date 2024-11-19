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
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\QrCode;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Scheb\TwoFactorBundle\Model\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;



class EmployeeController extends AbstractController
{
    
    private EntityManagerInterface $entityManager;
    private EmployeeRepository $employeeRepository;
    private GoogleAuthenticatorInterface $googleAuthenticator;
    

    public function __construct(EntityManagerInterface $entityManager, EmployeeRepository $employeeRepository, GoogleAuthenticatorInterface $googleAuthenticator)
    {

        $this->entityManager = $entityManager;
        $this->employeeRepository = $employeeRepository;
        $this->googleAuthenticator = $googleAuthenticator;
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
        'error' => $error,
    ]);
}

#[Route('/2fa/qrcode', name: '2fa_qrcode')]
public function displayGoogleAuthenticatorQrCode(GoogleAuthenticatorInterface $googleAuthenticator): Response
{
    // Generate the QR content
    $qrContent = $googleAuthenticator->getQRContent($this->getUser());

    // Create the QR Code object with content and configuration
    $qrCode = new QrCode($qrContent);
    
    // Use the PNG writer to create an image
    $writer = new PngWriter();
    $result = $writer->write($qrCode);

    // Return the PNG image response
    return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
}

#[Route('/2fa', name: '2fa_connect')]
public function displayGoogleAuthenticator(Request $request, GoogleAuthenticatorInterface $googleAuthenticator): Response
{
    // user is logged in but hasn't passed 2FA yet.
    if (!$this->getUser() || !$this->getUser()->isGoogleAuthenticatorEnabled()) {
        // Redirect if the user is not logged in or already passed 2FA
        return $this->redirectToRoute('app_project');
    }

    if ($request->isMethod('POST')) {
        $twoFactorCode = $request->get('2fa_code');
        
        // Check if the code from Google Authenticator is valid
        if ($googleAuthenticator->checkCode($this->getUser(), $twoFactorCode)) {
            // Successfully passed 2FA, now authenticate the user
            $token = new UsernamePasswordToken(
                $this->getUser(), 
                'main', 
                $this->getUser()->getRoles()
            );

            // Now that the user is authenticated, redirect to the project page
            return $this->redirectToRoute('app_project');
        } else {
            // If the code is incorrect, add an error message
            $this->addFlash('error', 'Incorrect 2FA code.');
        }
    }

    // Render the 2FA page with QR code URL
    return $this->render('auth/2fa.html.twig', [
        'qrCode' => $this->generateUrl('2fa_qrcode'),
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
    
    public function addEmployee(Request $request, UserPasswordHasherInterface $hasher, /*GoogleAuthenticatorInterface $googleAuth*/): Response
    {

        $employee = new Employee();
        $employee
            ->setContract('CDI')
            ->setArrivalDate(new \DateTimeImmutable());
            
        $form = $this->createForm(EmployeeAddType::class, $employee);
        $form->handleRequest($request);
        $secret = $this->googleAuthenticator->generateSecret();


        if ($form->isSubmitted() && $form->isValid()) {

            $employee->setPassword($hasher->hashPassword($employee, $employee->getPassword()));
            $employee->setGoogleAuthenticatorSecret($secret);

            $this->entityManager->persist($employee);
            $this->entityManager->flush();

            $this->addFlash('success', 'Compte créé avec succès !');

            return $this->redirectToRoute('connect_employee');
        }

        return $this->render('employee/employee-add.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout')]
    public function logout(): never
    {
        // Symfony gère la déconnexion.
    }
}
