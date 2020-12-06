<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
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
		    TextEditorField::new('content')->addCssClass('blog-text'),
		    DateTimeField::new('publishAt'),
		    BooleanField::new('isDraft'),
		    AssociationField::new('categories'),
	    ];
    }

    public function persistEntity( EntityManagerInterface $entityManager, $entityInstance ): void {
    	$entityInstance->setSlug(
    		$entityInstance->getPublishAt()->format('Y/m/d/') .
		    rawurlencode($entityInstance->getTitle())
        );

    	if (!$entityInstance->getPermanentSlug()) {
    		$entityInstance->setPermanentSlug(rawurlencode(uniqid()));
	    }

	    parent::persistEntity( $entityManager, $entityInstance );
    }

    public function updateEntity( EntityManagerInterface $entityManager, $entityInstance ): void {
	    $entityInstance->setSlug(
		    $entityInstance->getPublishAt()->format('Y/m/d/') .
		    rawurlencode($entityInstance->getTitle())
	    );

	    if (!$entityInstance->getPermanentSlug()) {
		    $entityInstance->setPermanentSlug(rawurlencode(uniqid()));
	    }

	    parent::updateEntity( $entityManager, $entityInstance );
    }

	public function createEntity( string $entityFqcn ) {
	    $blogPost = new BlogPost();
	    $blogPost->setUser($this->getUser() );
	    $blogPost->setCreatedAt(new DateTime());
	    $blogPost->setModifiedAt(new DateTime());
	    $blogPost->setPermanentSlug(uniqid());
	    return $blogPost;
    }
}
