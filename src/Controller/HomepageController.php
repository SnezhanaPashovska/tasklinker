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


    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        return $this->render('homepage/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
