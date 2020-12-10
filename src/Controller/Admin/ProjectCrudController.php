<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichImageField;
use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
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
		];
    }

}
