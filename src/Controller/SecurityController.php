<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
    * This controller allow  connexion
    * @param AuthenticationUtils $authenticationUtils
    * @return Response
    */

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
             //vide
    }

    /**
    * This controller allow registration
    *@param EntityManagerInterface $manager
    * @param Request $request
    * @return Response
    */
    #[Route('/inscription', name: 'security.registration', methods:['GET','POST'])] 
    public function registration(Request $request, EntityManagerInterface $manager) : Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
            $user = $form ->getData();

                    $manager->persist($user);
                    $manager->flush();
                
                    $this->addFlash(
                        'success',
                        ' Votre inscription  a été effectuée avec succès'
                    );
                    return $this->redirectToRoute('app_security');   
                };
        return $this->render('pages/security/registration.html.twig',[
            'form'=>$form->createView()
            
        ]);
    }

}
