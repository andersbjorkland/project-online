<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(BlogPostRepository $blogPostRepository): Response
    {
    	$latestPosts = $blogPostRepository->getLatestPaginated(1, 3);
        return $this->render('blog/index.html.twig', [
        	'title' => 'Blog',
        	'posts' => $latestPosts,
	        'showBlogIntro' => true
        ]);
    }

	/**
	 * @Route("/blog/{date}", name="blog_date", requirements={"date"=".+"})
	 */
	public function postsByDate(string $date, BlogPostRepository $blogPostRepository): Response {
		$posts = null;
		$string = $date;
		$errors = [];
		$title = "Blog";

		// Posts by year:
		if (strlen($date) === 4 || strlen($date) === 5) {
			try {
				if (strlen($date) === 4) {
					$date .= '/';
				}
				$date .= '01/01';

				$dateTime = new DateTime( $date );
				$posts = $blogPostRepository->getByYear($dateTime);
				$title .= " by year: " . $dateTime->format('Y');
			} catch ( Exception $e ) {
				$errors[] = $e;
			}
		}

		// Posts by month:
		if (strlen($date) >= 6 && strlen($date) <= 8) {
			try {
				if (strlen($date) === 7) {
					$date .= '/';
				}
				$date .= '1';

				$dateTime = new DateTime( $date );
				$posts = $blogPostRepository->getByMonth($dateTime);
				$title .= " by month: " . $dateTime->format('M')
				          . " (" . $dateTime->format('Y') . " )";

			} catch ( Exception $e ) {
				$errors[] = $e;
			}
		}

		if ($posts) {
			return $this->render('blog/index.html.twig', [
				'title' => $title,
				'posts' => $posts,
				'showBlogIntro' => true
			]);
		}

		if ($errors) {
			$this->addFlash("error", "Something went wrong.");
			return $this->redirectToRoute('blog');
		}

		// Posts by slug
		return $this->viewFromSlug($string, $blogPostRepository);
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
