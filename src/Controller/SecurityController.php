<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'app_security', methods:['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('pages/security/login.html.twig', [
            'Last_username' => $authenticationUtils->getLastUsername(),
            'error'=> $authenticationUtils ->getLastAuthenticationError()
        ]);
    }
     
    /**
     * @Route("/logout",name="logout")
     */
    #[Route('/deconnexion', name: 'security.logout')]
    public function logout()
    {

    }


}
