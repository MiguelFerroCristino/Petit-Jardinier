<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Repository\HaieRepository;

class MesureController extends AbstractController
{
    #[Route('/mesure', name: 'mesure')]
    public function index(Request $request, HaieRepository $haieRepository): Response
    {
        $request = Request::createFromGlobals();
        $maVariable=$request->get('maVariable');
        $haies = $haieRepository->findAll();
        $choix=$request->get('choix');

        return $this->render('mesure/index.html.twig', [
            'mesHaies' => $haies,
            'choix' => $choix
        ]);
    }
}
