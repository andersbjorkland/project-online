<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }



	/**
	 * @Route("/blog/{slug}", name="post_view", requirements={"slug"=".+"})
	 */
	public function viewFromSlug(string $slug, BlogPostRepository $blogPostRepository): Response
	{

		$blogPost = $blogPostRepository->findOneBy(["slug" => $slug]);

		if ($blogPost === null) {
			return $this->viewFromPermanentSlug($slug, $blogPostRepository);
		}

		return $this->presentView($blogPost, $blogPostRepository);
	}

	public function viewFromPermanentSlug(string $permanentSlug, BlogPostRepository $blogPostRepository): Response
	{

		$blogPost = $blogPostRepository->findOneBy(["permanentSlug" => $permanentSlug]);

		if ($blogPost === null) {
			return $this->redirectToRoute('blog', ["message" => "The blog post you were looking for could not be found"]);
		}

		return $this->presentView($blogPost, $blogPostRepository);
	}

	protected function presentView(BlogPost $blogPost, BlogPostRepository $blogPostRepository): Response
	{

		$posts = $blogPostRepository->getLatestPaginated();
		$totalPostCount = $blogPostRepository->getCount();
		$itemsPerPage = $this->getParameter('items_per_page');
		$numOfPostPages = intval($totalPostCount / $itemsPerPage);
		if ($totalPostCount % $itemsPerPage > 0) {
			$numOfPostPages += 1;
		}

		return $this->render('blog/view.html.twig', [
			'controller_name' => 'HomeController',
			'blogPost' => $blogPost,
			'posts' => $posts,
			'pages' => $numOfPostPages
		]);
	}
}
