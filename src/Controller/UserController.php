<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'app_user', methods:['GET','POST']) ]
    public function edit(User $user,Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher ): Response
    {
        if (!$this->getUser()){
         return $this->redirectToRoute('app_security');
        }

        if ($this->getUser() !== $user)
        {
            return $this->redirectToRoute('app_recipe');
        }

        $form = $this->createForm(UserType::class, $user);
    
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                 
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())){
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

    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'app_user_edit_password', methods:['GET','POST']) ]
    public function editPassword(User $user, Request $request,EntityManagerInterface $manager, UserPasswordHasherInterface $hasher) : Response
    {

        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
                 
            if ($hasher->isPasswordValid($user, $form->getData()['PlainPassword'])){
                $user->setUpdatedAt( new \DateTimeImmutable());
                $user->setPlainPassword(
                    $form ->getData()['newPassword']
                );
            
                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié'
                );

                $manager->persist($user);
                $manager->flush();

                return $this->redirectToRoute('app_recipe');

            }

            return $this->render('pages/user/edit_password.html.twig', [
                'form' =>$form->createView(),
            ]);

        }

    
   }
}