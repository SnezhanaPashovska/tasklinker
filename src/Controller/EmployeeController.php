<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeAddType;
use App\Form\EmployeeEditType;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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

        return $this->render('employee/employee-connection.html.twig', [
            'email' => $email,
            'error' => $error,
        ]);
    }

    #[Route('/2fa/qrcode', name: '2fa_qrcode')]
    public function displayGoogleAuthenticatorQrCode(GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        $qrContent = $googleAuthenticator->getQRContent($this->getUser());

        $qrCode = new QrCode($qrContent);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }

    #[Route('/2fa', name: '2fa_login')]
    public function displayGoogleAuthenticator(Request $request, GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        if (!$this->getUser()?->isGoogleAuthenticatorEnabled()) {
            return $this->redirectToRoute('app_project');
        }

        if ($request->isMethod('POST') && $googleAuthenticator->checkCode($this->getUser(), $request->get('2fa_code'))) {
            $token = new UsernamePasswordToken($this->getUser(), 'main', $this->getUser()->getRoles());
            $this->container->get('security.token_storage')->setToken($token);

            return $this->redirectToRoute('app_project');
        }

        if ($request->isMethod('POST')) {
            $this->addFlash('error', 'Incorrect 2FA code.');
        }

        return $this->render('auth/2fa.html.twig', [
            'qrCode' => $this->generateUrl('2fa_qrcode'),
        ]);
    }

    #[Route('/employees', name: 'employees')]
    #[IsGranted('ROLE_ADMIN')]
    public function listEmployees(): Response
    {
        if (!$this->getUser()) {
        } else {
        }
        $employees = $this->entityManager->getRepository(Employee::class)->findAll();

        return $this->render('employee/employees.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/employee/{id}/edit', name: 'employee_edit')]

    public function editEmployee(Request $request, Employee $employee): Response
    {

        $form = $this->createForm(EmployeeEditType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->redirectToRoute('employees');
        }

        return $this->render('employee/employee.html.twig', [
            'form' => $form->createView(),
            'employee' => $employee,
        ]);
    }

    #[Route('/employee/{id}/delete', name: 'employee_delete')]

    public function deleteEmployee(int $id): Response
    {
        $employee = $this->entityManager->getRepository(Employee::class)->find($id);

        if (!$employee) {
            return $this->redirectToRoute('employees');
        }

        $this->entityManager->remove($employee);
        $this->entityManager->flush();

        return $this->redirectToRoute('employees');
    }

    #[Route('/employee/add', name: 'add_employee')]

    public function addEmployee(Request $request, UserPasswordHasherInterface $hasher): Response
    {

        $employee = new Employee();
        $employee
            ->setContract('CDI')
            ->setArrivalDate(new \DateTimeImmutable())
            ->setRoles(['ROLE_USER'])
            ->setActive(true);

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
