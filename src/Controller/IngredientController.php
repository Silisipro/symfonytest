<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController

 

{
    /**
  * This controller display all ingrédients
  * @param IngredientRepository $repository
  * @param PaginatorInterface $paginator
  * @param Request $request
  * @return Response
  */
    #[Route('/ingredient', name: 'app_ingredient', methods:['GET'] )]
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request ): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findBy(['user'=> $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );
        
        
        return $this->render('pages/ingredient/index.html.twig', [
              'ingredients' =>$ingredients
        ]);

    }
        /**
         * This controller create new ingrédient
        * @param IngredientRepository $repository
        *@param EntityManagerInterface $manager
        * @param Request $request
        * @return Response
        */
     #[Route('/ingredient/nouveau', 'ingredient.new', methods:['GET', 'POST'])]
     #[IsGranted('ROLE_USER')]
    public function new (Request $request, EntityManagerInterface $manager) : Response 

    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form ->getData();
            $ingredient ->setUser($this->getUser());

        $manager->persist($ingredient);
        $manager->flush();
     
        $this->addFlash(
            'success',
            ' Votre ingrédient a été bien crée avec succès'
        );

        return $this->redirectToRoute('app_ingredient');   
        };  

 
        return $this->render('pages/ingredient/new.html.twig',[
            'form'=>$form->createView()
        ]);

    }
        /**
        * This controller edit ingrédient
        * @param IngredientRepository $repository
        *@param EntityManagerInterface $manager
        * @param Request $request
        * @return Response
        */
    #[Security("is_granted('ROLE_USER') and user===ingredient.getUser()")]
     #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods:['GET', 'POST'])]
    public function edit(Ingredient  $ingredient, Request $request, EntityManagerInterface $manager ) : Response
    {

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form ->getData();

                $manager->persist($ingredient);
                $manager->flush();
            
                $this->addFlash(
                    'success',
                    ' Votre ingrédient a été bien modifié avec succès'
                );

                return $this->redirectToRoute('app_ingredient');   
                };  

        return $this->render('pages/ingredient/edit.html.twig', [
            'form'=>$form->createView()
        ]);
        
    }
/**
    * This controller create new recipe
    * @param IngredientRepository $repository
    *@param EntityManagerInterface $manager
    * @param Request $request
    * @return Response
    */
    #[Route('/ingredient/supression/{id}', 'ingredient.delete', methods:['GET'])]
    public function delete(Ingredient  $ingredient, Request $request, EntityManagerInterface $manager ) : Response
    { 
        $manager->remove($ingredient);
        $manager->flush();
    
        $this->addFlash(
            'success',
            ' Votre ingrédient a été suprimé avec succès'
        );

        return $this->redirectToRoute('app_ingredient');   
    }  
    
    
    



}
