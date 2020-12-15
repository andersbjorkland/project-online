<?php

namespace App\Controller;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use App\Repository\CategoryRepository;
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
	        'page' => 'blog',
	        'title' => 'Blog',
        	'posts' => $latestPosts,
	        'showBlogIntro' => true
        ]);
    }

	/**
	 * @Route("blog/category/{name}/{page}", name="blog_category")
	 */
	public function postsByCategory(
		string $name,
		CategoryRepository $categoryRepository,
		BlogPostRepository $blogPostRepository,
		int $page=1
	): Response
	{
		$limit = $this->getParameter("items_per_page");
		$category = $categoryRepository->findOneBy(["name" => $name]);
		if ($category) {
			$numOfPages = $blogPostRepository->getByCategoryCountPages($category, $limit);
			$posts = $blogPostRepository->getByCategory($category, $page, $limit);

			return $this->render('blog/index.html.twig', [
				'page' => 'blog',
				'title' => "Blog by category: $name",
				'posts' => $posts,
				'showBlogIntro' => true,
				'slugName' => 'name',
				'slugValue' => $name,
				'pathName' => 'blog_category',
				'currentPage' => $page,
				'numOfPages' => $numOfPages,
			]);
		} else {
			$this->addFlash("notice", "The category '$name' could not be found.");
			return $this->redirectToRoute('blog');
		}
	}

	/**
	 * @Route("/blog/date/{year}", name="blog_year")
	 */
	public function postsByYear(int $year, BlogPostRepository $blogPostRepository): Response {
		$page = 1;
		$posts = null;
		$string = $year;
		$date = $year;
		$errors = [];
		$title = "Blog";
		$limit = $this->getParameter("items_per_page");
		$numOfPages = 1;

		// Posts by year:
		try {
			$date .= '/';
			$date .= '01/01';

			$dateTime = new DateTime( $date );
			$posts = $blogPostRepository->getByYear($dateTime, $page, $limit);
			$numOfPages = $blogPostRepository->getByYearCountPages($dateTime, $limit);
			$title .= " by year: " . $dateTime->format('Y');
		} catch ( Exception $e ) {
			$errors[] = $e;
		}

		if ($posts) {
			return $this->render('blog/index.html.twig', [
				'page' => 'blog',
				'title' => $title,
				'posts' => $posts,
				'showBlogIntro' => true,
				'slugName' => 'year',
				'slugValue' => $string,
				'pathName' => 'blog_year_paginated',
				'currentPage' => $page,
				'numOfPages' => $numOfPages,
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
	 * @Route("/blog/date/{year}/p={page}", name="blog_year_paginated")
	 */
	public function postsByYearPaginated(int $year, BlogPostRepository $blogPostRepository, int $page = 1): Response {
		$posts = null;
		$string = $year;
		$errors = [];
		$title = "Blog";
		$limit = $this->getParameter("items_per_page");
		$numOfPages = 1;

		// Posts by year:
		try {
			$date = "$year/01/01";

			$dateTime = new DateTime( $date );
			$posts = $blogPostRepository->getByYear($dateTime, $page, $limit);
			$numOfPages = $blogPostRepository->getByYearCountPages($dateTime, $limit);
			$title .= " by year: " . $dateTime->format('Y');
		} catch ( Exception $e ) {
			$errors[] = $e;
		}

		if ($posts) {
			return $this->render('blog/index.html.twig', [
				'page' => 'blog',
				'title' => $title,
				'posts' => $posts,
				'showBlogIntro' => true,
				'slugName' => 'year',
				'slugValue' => $string,
				'pathName' => 'blog_year_paginated',
				'currentPage' => $page,
				'numOfPages' => $numOfPages,
			]);
		}

		if ($errors) {
			$this->addFlash("error", "No posts were found for $string.");
			return $this->redirectToRoute('blog');
		}

		// Posts by slug
		return $this->viewFromSlug($string, $blogPostRepository);
	}

	/**
	 * @Route("/blog/date/{date}", name="blog_month", requirements={"date"=".+"})
	 */
	public function postsByMonth(string $date, BlogPostRepository $blogPostRepository): Response {
		$page = 1;
		$posts = null;
		$string = $date;
		$errors = [];
		$title = "Blog";
		$limit = $this->getParameter("items_per_page");
		$numOfPages = 1;

		// Posts by month:
		try {
			$date .= "/1";

			$dateTime = new DateTime( $date );
			$posts = $blogPostRepository->getByMonth($dateTime, $page, $limit);
			$numOfPages = $blogPostRepository->getByMonthCountPages($dateTime, $limit);
			$title .= " by month: " . $dateTime->format('M')
			          . " (" . $dateTime->format('Y') . " )";

		} catch ( Exception $e ) {
			$errors[] = $e;
		}

		if ($posts) {
			return $this->render('blog/index.html.twig', [
				'page' => 'blog',
				'title' => $title,
				'posts' => $posts,
				'showBlogIntro' => true,
				'slugName' => 'date',
				'slugValue' => $string,
				'pathName' => 'blog_month_paginated',
				'currentPage' => $page,
				'numOfPages' => $numOfPages,
			]);
		}

		if ($errors) {
			$this->addFlash("error", "No posts were found $string.");
			return $this->redirectToRoute('blog');
		}

		// Posts by slug
		return $this->viewFromSlug($string, $blogPostRepository);
	}

	/**
	 * @Route("/blog/date/{date}/p={page}", name="blog_month_paginated", requirements={"date"=".+"})
	 */
	public function postsByMonthPaginated(string $date, BlogPostRepository $blogPostRepository, int $page = 1): Response {
		$posts = null;
		$errors = [];
		$title = "Blog";
		$limit = $this->getParameter("items_per_page");
		$numOfPages = 1;
		$string = $date;

		// Posts by month:
		try {

			$date .= "/1";

			$dateTime = new DateTime( $date );
			$posts = $blogPostRepository->getByMonth($dateTime, $page, $limit);
			$numOfPages = $blogPostRepository->getByMonthCountPages($dateTime, $limit);
			$title .= " by month: " . $dateTime->format('M')
			          . " (" . $dateTime->format('Y') . " )";

		} catch ( Exception $e ) {
			$errors[] = $e;
		}

		if ($posts) {
			return $this->render('blog/index.html.twig', [
				'page' => 'blog',
				'title' => $title,
				'posts' => $posts,
				'showBlogIntro' => true,
				'slugName' => 'date',
				'slugValue' => $string,
				'pathName' => 'blog_month_paginated',
				'currentPage' => $page,
				'numOfPages' => $numOfPages,
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
			$this->addFlash('error', $permanentSlug);
			return $this->redirectToRoute('blog');
		}

		return $this->presentView($blogPost, $blogPostRepository);
	}

	protected function presentView(BlogPost $blogPost, BlogPostRepository $blogPostRepository): Response
	{
		$baseUrl = $this->getParameter("app.path.thumbnails");
		$domain = $_SERVER["SERVER_NAME"];

		$posts = $blogPostRepository->getLatestPaginated();
		$totalPostCount = $blogPostRepository->getCount();
		$itemsPerPage = $this->getParameter('items_per_page');
		$numOfPostPages = intval($totalPostCount / $itemsPerPage);
		if ($totalPostCount % $itemsPerPage > 0) {
			$numOfPostPages += 1;
		}

		return $this->render('blog/view.html.twig', [
			'page' => 'blog',
			'blogPost' => $blogPost,
			'baseUrl' => $baseUrl,
			'posts' => $posts,
			'pages' => $numOfPostPages,
			'domain' => $domain
		]);
	}
}
