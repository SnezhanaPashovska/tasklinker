<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

class HomepageController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'landing_page')]
    public function main(): Response
    {
        
        return $this->render('homepage/main.html.twig');
    }

    #[Route('/connection', name: 'login_page')]
    public function login(): Response
    {
        
        return $this->render('employee/employee-connection.html.twig');
    }


    #[Route('/projects', name: 'homepage')]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
       
        $projects = $this->entityManager->getRepository(Project::class)->findBy(['active' => true]);

        return $this->render('homepage/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/access-denied', name: 'access_denied')]
    public function accessDenied(): Response
    {
        return $this->render('error/access_denied.html.twig');
    }

    #[Route('/logout', name: 'logout')]
public function logout(): void
{
    
}
    
}
