<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET','POST'])]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $contact = new Contact();

       //if($this->getUser()){
      //  $contact->setFullName($this->getUser()->getFullName())
      //         ->setEmail($this->getUser()->getEmail());
      // }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
              $contact = $form ->getData();

                $manager->persist($contact);
                $manager->flush();  
            
                $this->addFlash(
                    'success',
                    ' Votre demande est envoyéé avec succès'
                );
                return $this->redirectToRoute('app_contact');   
            };
        

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
