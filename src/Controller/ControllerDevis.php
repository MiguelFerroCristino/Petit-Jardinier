<?php

namespace App\Controller;

use App\Entity\Haie;
use App\Entity\Tailler;
use App\Entity\Devis;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HaieRepository;
use App\Repository\DevisRepository;
use App\Repository\TaillerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;


class ControllerDevis extends AbstractController
{

    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/devis', name: 'app_devis')]
    public function index(HaieRepository $haieRepo): Response
    {

        $request = Request::createFromGlobals();

        $longueurs = $request->query->get('longueurs');
        $hauteurs = $request->query->get('hauteurs');
        $haies = $request->query->get('haie');
        
        $session = new Session();
        $ParticulierOuEntrprise = $session->get('nomVariable'); 

        $tabsHaie = [];
        //pour chaque haie on crée un objet haie avec sa longueur et sa hauteur
        for ($i = 1; $i <= count($haies); $i++) {
            $tabsHaie[] = [
                'haie' => $haies[$i],
                'longueur' => $longueurs[$i],
                'hauteur' => $hauteurs[$i]
            ];
        }

        //calcul du prix total
        $prixFinal = 0;
        foreach ($tabsHaie as $haie) {
            $haiePrice = $haieRepo->find($haie['haie'])->getPrix();
            $prix = $haie['longueur'] * $haiePrice;
            if ($haie['hauteur'] > 1.5) {
                $prix *= 1.5;
            }
            if ($ParticulierOuEntrprise === 'Entreprise') {
                $prix *= 0.9;
            }
            $prixFinal += $prix;
        }
        return $this->render('devis/index.html.twig', [ // Utilisation de crochets pour définir un tableau

            'devis' => [
                'nomination' =>$ParticulierOuEntrprise,
                'haies' => $tabsHaie,
                'prix' => $prixFinal,
            ]
        ]);
        
        
    }

    #[Route('/devis/create', name: 'app_devis_create')]
    public function create(Request $request,HaieRepository $haieRepo ): Response
    {
        $user = $this->getUser();
        if ($user == null){
        return $this->redirectToRoute('app_register');
        }


        $entityManager = $this->entityManager;

        //calcul du prix total
        $prixFinal = 0;

        // Récupérer les données du formulaire
        $haieList = $request->get('haie');
        $choix = $request->get('choix');
        
        foreach ($haieList as $haie) {

            $Lahaie = $haieRepo->find($haie);
            $haiePrice = $Lahaie->getPrix();
            $prix = $Lahaie->getLongueur() * $haiePrice;
            if ( $Lahaie->getHauteur() > 1.5) {
                $prix *= 1.5;
            }
            if ($choix === 'Entreprise') {
                $prix *= 0.9;
            }
            
            $prixFinal += $prix;
        }

        // Créer une instance de Devis et définir ses propriétés
        $devis = new Devis();
        $devis->setDate(new \DateTime());
        $devis->setUser($this->getUser());
        $devis->setTypeClient($choix);
        $devis->setPrixTotal($prixFinal);

        // Enregistrer le devis en base de données
        $entityManager->persist($devis);
        $entityManager->flush();
        

        $tabTailler = [];

        foreach ($haieList as $haie) {

            $Lahaie = $haieRepo->find($haie);
            $longueur = $Lahaie->getLongueur();
            $hauteur = $Lahaie->getHauteur();
            if($longueur != 0   || $hauteur != 0){
            $tailler = new Tailler();
            $tailler->setDevis($devis);
            $tailler->setHaie($Lahaie);
            $tailler->setHauteur($hauteur);
            $tailler->setLongueur($longueur);
            $tabTailler[] = $tailler;  
            $entityManager->persist($tailler);
            $entityManager->flush();
        }

    }
        return $this->render('devis_recap/index.html.twig', [
            'devis' => $devis,
            'tailler'=> $tabTailler
        ]);
    }

    #[Route('/devis/list', name: 'app_devis_list')]
    public function list(DevisRepository $devis): Response
    {
        $user = $this->getUser();
        $devis = $devis->findByUser($user);
        return $this->render('devis/list.html.twig', [
            'devis' => $devis,
        ]);
    }

    //supprimer un devis
    #[Route('/devis/delete/{id}', name: 'app_devis_delete')]
    public function delete($id, DevisRepository $DevisRepo, TaillerRepository $TaillerRepo): Response
    {
        $entityManager = $this->entityManager;

        //vérifier si l'utilisateur est admin
        $user = $this->getUser();
        if ($user === null || !$user->hasAdminRole() ) {
            return $this->redirectToRoute('accueil');
        }

        $devis = $DevisRepo->find($id);
        $tailler = $TaillerRepo->findBy(['devis' => $devis->getId()]);
        foreach ($tailler as $taille) {
            $entityManager->remove($taille);
            $entityManager->flush();
        }
        $entityManager->remove($devis);
        $entityManager->flush();
        return $this->redirectToRoute('app_devis_list');
    }

    #[Route('/devis/detail/{id}', name: 'app_devis_detail')]
    public function detail($id, DevisRepository $DevisRepo,TaillerRepository $TaillerRepo): Response
    {
        
        $devis = $DevisRepo->find($id);
        $tailler = $TaillerRepo->findBy(['devis' => $devis->getId()]);
        $tabTailler = [];
        foreach ($tailler as $taille) {
            $tabTailler[] = [
                'haie' => $taille->getHaie()->getNom(),
                'longueur' => $taille->getLongueur(),
                'hauteur' => $taille->getHauteur(),
            ];
        }

        return $this->render('devis/detail.html.twig', [
            'devis' => $devis,
            'tailler' => $tabTailler,
        ]);
    }



}