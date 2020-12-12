<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortfolioController extends AbstractController
{
    /**
     * @Route("/portfolio", name="portfolio")
     */
    public function index(ProjectRepository $projectRepository): Response
    {
    	$projects = $projectRepository->getProjects();

    	$baseUrl = $this->getParameter("app.path.thumbnails");

        return $this->render('portfolio/index.html.twig', [
	        'page' => 'portfolio',
	        'baseUrl' => $baseUrl,
            'projects' => $projects,
        ]);
    }
}
