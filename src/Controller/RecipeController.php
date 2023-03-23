<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
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

    #[IsGranted('ROLE_USER')]
    #[Route('/recette/publique', name: 'recipe.index.public', methods: ['GET'])]
    public function indexPublic(RecipeRepository $repository, PaginatorInterface $paginator, Request $request ): Response
    {

        $recipes = $paginator->paginate(
            $repository->findPublicRecipe(null),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/recipe/index_public.html.twig', [
            'recipes' => $recipes
            ]) ;   
    }

    /**
    * This controller show recipe  
    * @param MarkRepository $repository
    *@param EntityManagerInterface $manager
    * @param Request $request
    * @return Response
    */

    #[Security("is_granted('ROLE_USER') and recipe.isIsPublic() ===true || user===recipe.getUser()")]
    #[Route('/recette/{id}', name: 'recipe.show', methods: ['GET', 'POST'])]
    public function show (Recipe $recipe, Request $request, EntityManagerInterface $manager, MarkRepository $markRepository): Response
    
    {
        $mark = new Mark();
         $form = $this->createForm(MarkType::class, $mark);
         $form->handleRequest($request);
                    if ($form->isSubmitted() && $form->isValid()) {
                        $mark->setUser($this->getUser())
                              ->setRecipe($recipe);
                        
                        $existingMark = $markRepository->findOneBy([

                            'user' => $this->getUser(),
                            'recipe' => $recipe
                        ]);      
                       if (!$existingMark){
                        $manager ->persist($mark);
                       }else{
                        $existingMark-> setMark(
                            $form ->getData()->getMark()
                        );
                       }
                       $manager ->flush();
                    $this->addFlash(
                        'success',
                        ' Votre note a été bien prise en compte'
                    );
                    return $this->redirectToRoute('recipe.show', ['id'=>$recipe->getId()]);
                }
        return $this->render('pages/recipe/show.html.twig',[
            'recipe' => $recipe,
            'form'=>$form->createView()

         ]);  
         
    }
 
    /**
    * This controller create new recipe
    *@param EntityManagerInterface $manager
    * @param Request $request
    * @return Response
    */
     #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
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
        /**
    * This controller delete recipe
    * @param Recipe $recipe
    *@param EntityManagerInterface $manager
    * @param Request $request
    * @return Response
    */

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
