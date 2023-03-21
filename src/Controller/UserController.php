<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{
    /**
    * This controller create new recipe
    * @param IngredientRepository $repository
    *@param EntityManagerInterface $manager
    * @param Request $request
    * @return Response
    */

    #[Security("is_granted('ROLE_USER') and user===choosenUser")]
    #[Route('/utilisateur/edition/{id}', name: 'app_user', methods:['GET','POST']) ]
    public function edit(User $choosenUser,Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher ): Response
    {
       /**  ceci peut remplacer security
        *  if (!$this->getUser()){
         *return $this->redirectToRoute('app_security');
       * }

       * if ($this->getUser() !== $user)
       *{
        *    return $this->redirectToRoute('app_recipe');
        *}
        */

        $form = $this->createForm(UserType::class, $choosenUser);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                 
            if ($hasher->isPasswordValid($choosenUser , $form->getData()->getPlainPassword())){
                $user = $form ->getData();
                $manager->persist($user);
                $manager->flush();
            
                $this->addFlash(
                    'success',
                    'Les information de votre compte ont été modifiées avec succès'
                );
                return $this->redirectToRoute('app_recipe');
            }else{
                $this->addFlash(
                    'warning',
                    'Le mot de passe incorret'
                );
            }      
        }
        return $this->render('pages/user/edit.html.twig', [
            'form' =>$form->createView(),
        ]);
    }

    /**
    * This controller create new recipe
    * @param IngredientRepository $repository
    *@param EntityManagerInterface $manager
    * @param Request $request
    * @return Response
    */
    
    #[Security("is_granted('ROLE_USER') and user===choosenUser")]
    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'app_user_edit_password', methods:['GET','POST']) ]
    public function editPassword(User $choosenUser, Request $request,EntityManagerInterface $manager, UserPasswordHasherInterface $hasher) : Response
    {

        $form = $this->createForm(UserPasswordType::class, $choosenUser);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                 
            if ($hasher->isPasswordValid($choosenUser, $form->getData()['PlainPassword'])){
                $choosenUser->setUpdatedAt( new \DateTimeImmutable());
                $choosenUser->setPlainPassword(
                    $form ->getData()['newPassword']
                );
            
                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié'
                );

                $manager->persist($choosenUser);
                $manager->flush();

                return $this->redirectToRoute('app_recipe');

            }

            return $this->render('pages/user/edit_password.html.twig', [
                'form' =>$form->createView(),
            ]);

        }

    
   }
}