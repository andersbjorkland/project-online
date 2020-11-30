<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    public function configureAssets( Assets $assets ): Assets {
	    return $assets->addJsFile('assets/js/uploader.js');
    }

	public function configureFields(string $pageName): iterable
    {
	    return [
		    TextField::new('title'),
		    TextareaField::new('summary'),
		    TextEditorField::new('text')->addCssClass('blog-text'),
	    ];
    }

    public function createEntity( string $entityFqcn ) {
	    $blogPost = new BlogPost();
	    $blogPost->setIsDraft(false);
	    $blogPost->setUser((User::class) ($this->getUser()) );
	    return $blogPost;
    }
}
