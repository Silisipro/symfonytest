<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use App\Repository\RecipeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class RecipeController extends AbstractController

/**
  * This controller display all recipes
  * @param IngredientRepository $repository
  * @param PaginatorInterface $paginator
  * @param Request $request
  * @return Response
  */
{
    #[Route('/recette', name: 'app_recipe', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(RecipeRepository $repository, PaginatorInterface $paginator, Request $request ): Response
    {

        $recipes = $paginator->paginate(
            $repository->findBy(['user'=> $this->getUser()]),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }

    /**
    * This controller create new recipe
    * @param IngredientRepository $repository
    *@param EntityManagerInterface $manager
    * @param Request $request
    * @return Response
    */
     #[Route('/recette/creation', 'recipe.new', methods: ['GET', 'POST'])]
     #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $manager ): Response
    {
                $recipe = new Recipe();
                $form = $this->createForm(RecipeType::class, $recipe);
                $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $recipe = $form ->getData();
                        $recipe->setUser($this->getUser());

                    $manager->persist($recipe);
                    $manager->flush();
                
                    $this->addFlash(
                        'success',
                        ' Votre recette a été bien crée avec succès'
                    );
                    return $this->redirectToRoute('app_recipe');   
                };



        return $this->render('pages/recipe/new.html.twig', [
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
        #[Security("is_granted('ROLE_USER') and user===recipe.getUser()")]
        #[Route('/recette/edition/{id}', 'recipe.edit', methods:['GET', 'POST'])]
        
        public function edit(Recipe  $recipe, Request $request, EntityManagerInterface $manager ) : Response
        {
    
            $form = $this->createForm(RecipeType::class, $recipe);
    
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $recipe = $form ->getData();
    
                    $manager->persist($recipe);
                    $manager->flush();
                
                    $this->addFlash(
                        'success',
                        ' Votre ingrédient a été bien modifié avec succès'
                    );
    
                    return $this->redirectToRoute('app_recipe');   
                    };  
    
            return $this->render('pages/recipe/edit.html.twig', [
                'form'=>$form->createView()
            ]);
            
        }

        #[Route('/recipe/supression/{id}', 'recipe.delete', methods:['GET'])]
    public function delete(Recipe  $recipe, Request $request, EntityManagerInterface $manager ) : Response
    { 
        $manager->remove($recipe);
        $manager->flush();
    
        $this->addFlash(
            'success',
            ' Votre recette a été suprimé avec succès'
        );

        return $this->redirectToRoute('app_recipe');   
    }  







}
