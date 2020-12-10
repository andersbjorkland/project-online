<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

	public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Project Online');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
	    yield MenuItem::section('Blog');
	    yield MenuItem::linkToCrud('Categories', 'fa fa-tags', Category::class);
	    yield MenuItem::linkToCrud('Blog Posts', 'fa fa-file-text', BlogPost::class);
	    yield MenuItem::section('Project');
	    yield MenuItem::linkToCrud('Projects', 'fa fa-tags', Project::class);

    }
}
