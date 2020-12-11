<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichImageField;
use App\Entity\Category;
use App\Entity\Project;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

	public function configureFields(string $pageName): iterable
    {
		return [
			TextField::new('name'),
			TextEditorField::new('description'),
			VichImageField::new('thumbnailFile')->onlyOnForms(),
			ImageField::new('thumbnail')->setBasePath('/uploads/images/thumbnails')->hideOnForm(),
			Field::new('github'),
			Field::new('url'),
			Field::new('categories'),
			Field::new('isDraft'),
			CollectionField::new('categories')
			               ->allowAdd()
			               ->allowDelete()
			               ->setEntryType(CategoryType::class)
		];
    }

    public function persistEntity( EntityManagerInterface $entityManager, $entityInstance ): void {
	    $this->handleCategories($entityManager, $entityInstance, true);
    }

    public function updateEntity( EntityManagerInterface $entityManager, $entityInstance ): void {
	    $this->handleCategories($entityManager, $entityInstance, false);
    }

	protected function handleCategories(EntityManagerInterface $entityManager, $entityInstance, $shouldPersist) {
	    $categoryRepository = $entityManager->getRepository(Category::class);
	    foreach ($entityInstance->getCategories() as $category) {
		    $tempCategory = $categoryRepository->findOneBy( [ "name" => $category->getName() ] );
		    if ( $tempCategory ) {
			    $entityInstance->removeCategory( $category );
			    $entityInstance->addCategory( $tempCategory );
		    }
	    }

	    if ($shouldPersist) {
		    parent::persistEntity( $entityManager, $entityInstance );
	    } else {
		    parent::updateEntity( $entityManager, $entityInstance );
	    }
    }

}
