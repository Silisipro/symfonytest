<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Demandes de contact')
            ->setEntityLabelInSingular('Demande de contact')
            ->setPageTitle('index', 'Sylove | Administration des demandes de contact')
            ->setPaginatorPageSize(30);
            
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
           
        IdField::new('id')
            ->hideOnIndex(),
        TextField::new('fullName', 'Nom'),
        TextField::new('email')
            ->setFormTypeOption('disabled', 'disabled'),
        DateTimeField::new('createdAt','Date d\'envoie') 
            ->hideOnForm(),            
        TextareaField::new('message','Objet')
            ->hideOnIndex(),            

        ];
    }

}
