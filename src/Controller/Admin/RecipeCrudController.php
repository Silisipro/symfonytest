<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RecipeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recipe::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Recettes')
            ->setEntityLabelInSingular('Recette')
            ->setPageTitle('index', 'Sylove | Administration des recettes')
            ->setPaginatorPageSize(10);
            
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
           
        IdField::new('id'),
        TextField::new('name','Nom'),
        IntegerField::new('time','Temps')
        ->setFormTypeOption('disabled', 'disabled'),
        IntegerField::new('nbPeople','Personne')
        ->setFormTypeOption('disabled', 'disabled'),
        IntegerField::new('difficulty', 'Difficulté')
        ->setFormTypeOption('disabled', 'disabled'),
        BooleanField::new('isFavorite','Favoris')
            ->setFormTypeOption('disabled', 'disabled'),
        BooleanField::new('isPublic', 'Publique'),
        AssociationField::new('ingredients','Ingrédient'),
        IntegerField::new('price','Prix')
            ->setFormTypeOption('disabled', 'disabled'),
        DateTimeField::new('createdAt','Date de création') 
            ->hideOnForm(),         
        TextareaField::new('description','Description')
            ->hideOnIndex(),            
            AssociationField::new('user','Ajouter par')
            ->hideOnForm(),         

        ];
    }




}
