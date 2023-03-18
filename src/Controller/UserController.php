<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'app_user', methods:['GET','POST']) ]
    public function edit(User $user,Request $request, EntityManagerInterface $manager): Response
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
                $user = $form ->getData();

                $manager->persist($user);
                $manager->flush();
            
                $this->addFlash(
                    'success',
                    'Les information de votre compte ont été modifiées avec succès'
                );
                return $this->redirectToRoute('app_recipe');   
            }


        return $this->render('pages/user/edit.html.twig', [
            'form' =>$form->createView(),
        ]);
    }
}
