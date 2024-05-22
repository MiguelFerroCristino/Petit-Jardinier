<?php
 
namespace App\Controller;
 
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
 
class AccueilController extends AbstractController
{
    #[Route('/accueil', name: 'accueil')]
    public function index(): Response
    {
        // Voici les instructions aiguillant vers la vue.
        // On passe à la vue une variable "controller_name" contenant la valeur "AccueilController".
        // Cette vue affiche un message "Hello" suivi du nom du contrôleur
        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }
}