<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(BlogPostRepository $blogPostRepository): Response
    {

		$posts = $blogPostRepository->getLatestPaginated();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
	        'posts' => $posts
        ]);
    }
}
