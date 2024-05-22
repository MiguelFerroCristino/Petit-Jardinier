<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Haie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HaieControlleurController extends AbstractController
{
    private $code;
    private $prix;
    private $nom;
    private $categorie;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setCode(string $haieCode): void
    { 
        $this->code =$haieCode;  
    }

    public function setNom(string $haieNom): void
    { 
        $this->nom = $haieNom;
    }

    public function setPrix(float $haiePrix): void
    {
        $this->prix = $haiePrix;
    }

    public function setCategorie(string $haieCategorie): void
    {
        $this->categorie = $haieCategorie;
    }

    #[Route('/creation/haie', name: 'app_creation_haie')]
    public function index(Request $request): Response
    {

        $haie = new Haie();
        $haieCode = $request->request->get('haie_code');
        $haieNom = $request->request->get("haie_nom");
        $haiePrix = $request->request->get('haie_prix');
        $hauteur = $request->request->get('hauteur');
        $longueur = $request->request->get('longueur');
        $haieCategorie = $request->request->get('select-haie');


        if ($haieCode !== null) {
            $haie->setCode($haieCode);
        }
        
        if ($haieNom !== null) {
            $haie->setNom($haieNom);
        }
        
        if ($haiePrix !== null && is_numeric($haiePrix)) {
            $prixFloat = floatval($haiePrix);
            
            if ($prixFloat >= 0) {
                $haie->setPrix($prixFloat);
            }
        }

        if ($haieCategorie !== null) {
            $haie->setCategorie($haieCategorie);
        }

        if ($hauteur !== null) {
            $haie->setHauteur($hauteur);
        }

        if ($longueur !== null) {
            $haie->setLongueur($longueur);
        }
    
    
    
        $entityManager = $this->entityManager;

        if ($haie->getNom() !== ""){
        $entityManager->persist($haie);
        $entityManager->flush();
        }

        return $this->render('creation_haie/index.html.twig', [
            'controller_name' => 'CreationHaieController',
        ]);
    }

    #[Route('/voir/haie', name: 'app_voir_haie')]
    public function haieVoir(EntityManagerInterface $entityManager): Response
    {

        $haies = $this->entityManager->getRepository(Haie::class)->findAll();
        return $this->render('voir_haie/index.html.twig', [
            'haies' => $haies,
        ]);
}


#[Route('/modifier/{code}', name: 'modifier')]
public function modifierHaie($code, Request $request, EntityManagerInterface $entityManager): Response
{
    $haie = $entityManager->getRepository(Haie::class)->findOneBy(['code' => $code]);

    if (!$haie) {
        throw $this->createNotFoundException('Haie not found for code ' . $code);
    }

    if ($request->getMethod() === 'POST' && $request->request->has('valider')) {

        $haie->setNom($request->request->get('haie_nom'));
        $haie->setPrix($request->request->get('haie_prix'));
        $haie->setCategorie($request->request->get('select-haie'));

        $entityManager->flush();

        return $this->redirectToRoute('app_voir_haie');
    }

    return $this->render('modif_haie/index.html.twig', [
        'haie' => $haie,
    ]);
}


    
    
    #[Route('/supprimer/haie/{code}', name: 'app_supp_haie')]
    public function suppHaie($code, EntityManagerInterface $entityManager): Response
    {
        $haie = $entityManager->getRepository(Haie::class)->findOneBy(['code' => $code]);
        if ($haie) {
            $entityManager->remove($haie);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_voir_haie');
    }
    

}
