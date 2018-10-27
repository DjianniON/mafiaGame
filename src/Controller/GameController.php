<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Entity\Partie;
use App\Repository\CarteRepository;
use App\Repository\JetonRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/game")
 */
class GameController extends AbstractController
{
    /**
     * @Route("/", name="game_index")
     */
    public function index()
    {
        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
        ]);
    }
    /**
     * @Route("/new-game", name="partie_creer")
     */
    public function creerPartie(UserRepository $userRepository, Request $request, JetonRepository $jetonRepository, CarteRepository $carteRepository)
    {
        if($request->getMethod() === 'POST') {

            $game = new Partie();
            $game->setDate(new \DateTime('now'));

            $game->setHistoriquePartie(['nbTour' => 1]);
            $game->setStatus(['status' => 'T', 'nbManche' => 1]);
            $game->setDefausse(false);


            //construction terrain
            $cartes = $carteRepository->findBy([],['type' => 'DESC']);

            $tTerrain = [];
            for($i=0;$i<3;$i++)
            {
                $tTerrain[] = array_pop($cartes);
            }
            shuffle($cartes);
            for($i=0;$i<2;$i++){
                $tTerrain[] = array_pop($cartes);
            }
            $game->setTerrain($tTerrain);

            $tMain = [];
            $tChameau = [];

            for($i=0; $i<5; $i++){
                $carte = array_pop($cartes);
                if($carte->getType()->getNom() === 'Cadillac'){
                    $tChameau[] = $carte;
                }
                else{
                    $tMain[] = $carte;
                }
            }
            $em = $this->getDoctrine()->getManager();

            $p1 = new Joueur();
            $p1->setUsers($this->getUser());
            $p1->setMain($tMain);
            $p1->setChameaux($tChameau);
            $p1->setScore(0);
            $p1->setTasJetons([]);
            $tMain = [];
            $tChameau = [];

            for($i=0; $i<5; $i++){
                $carte = array_pop($cartes);
                if($carte->getType()->getNom() === 'Cadillac'){
                    $tChameau[] = $carte;
                }
                else{
                    $tMain[] = $carte;
                }
            }

            $p2 = new Joueur();
            $p2->setUsers($userRepository->find($request->request->get('player2')));
            $p2->setMain($tMain);
            $p2->setChameaux($tChameau);
            $p2->setScore(0);
            $p2->setTasJetons([]);
            $game->addJoueur($p1);
            $game->addJoueur($p2);

            $game->setMainJoueur(true);

            $tDeck = [];
            $nbCartes = count($cartes);
            for($i=0; $i<$nbCartes; $i++) {
                $tDeck[] = array_pop($cartes);
            }
            $game->setDeck($tDeck);

            $jetons = $jetonRepository->findByTypeValue();
            $game->setTasJeton($jetons);


            $em->persist($game);
            $em->flush();

            dump($game);
            return $this->redirectToRoute('show_game', ['partie' => $game->getId()]);
        }
        return $this->render('game/creer-partie.html.twig', [
            'joueurs' => $userRepository->findAll()
        ]);
    }

    /**
     * @Route("/show-game/{partie}", name="show_game")
     */
    public function afficherPartie(Partie $partie) {
        return $this->render('game/afficher_partie.html.twig', ['partie' => $partie]);
    }

    /**
     * @Route("/show-board/{partie}", name="show_board")
     */
    public function afficherPlateau(JetonRepository $jetonRepository, CarteRepository $carteRepository, Partie $partie) {
        return $this->render('game/show_board.html.twig',
            ['partie' => $partie,
                'jetons' => $jetonRepository->findByArrayId(),
                'cartes' => $carteRepository->findByArrayId(),
            ]);
    }

    /**
     * @Route("/actualise-plateau/{partie}", name="actualise_plateau")
     */
    public function actualisePlateau(Partie $partie) {
        //$partie->getStatus()['status'];
        while($partie->getStatus(['status']) != 'F') {//todo:strange behaviour => ça me retourne bien 'T' quand j'utilise la ligne au dessus...
            if ($this->getUser() == $partie->getJoueurs()[0]->getUsers()) {
                switch ($partie->getMainJoueur()) {
                    //tester si je suis J1 ou J2 et en fonction adapter les return.
                    case true:
                        return $this->json('montour');
                    case false:
                        return $this->json('touradversaire');
                    default:
                        return $this->json('E');
                }
            }
            else{
                switch ($partie->getMainJoueur()) {
                    case true:
                        return $this->json('touradversaire');
                    case false:
                        return $this->json('montour');
                    default:
                        return $this->json('E');
                }
            }
        }
    }

    /**
     * @Route("/action/prendre/{partie}", name="action_prendre")
     */
    public function jouerActionPrendre(EntityManagerInterface $entityManager, CarteRepository $carteRepository, Request $request, Partie $partie) {
        $idcarte = $request->request->get('cartes');

            for($i= 0 ; $i < count($idcarte); $i++)
            {
                $cartes[] = $carteRepository->find($idcarte[$i]);
            }



        if ($this->getUser() == $partie->getJoueurs()[0]->getUsers()) //Condition de vérification du joueur
        {
            $joueur = $partie->getJoueurs()[0];
        }
        else{
            $joueur = $partie->getJoueurs()[1];
        }


        if ($cartes !== null && count($cartes) === 1) {//todo:vérif monocarte
            $main = $joueur->getMain();
            //vérifier s'il y a 7 cartes dans la main.
            if (count($main) < 7) {
                $carte = array_shift($cartes);
                $main[] = $carte; //on ajoute dans la main du joueur
                $terrain = array_values($partie->getTerrain());
                $carteId = $carte->getId();
                for($i=0;$i < count($terrain);$i++)
                {
                    $terrainId[] = $terrain[$i]->getId();
                    //$terId[] = $terrainId->getId();
                }
                $cartu = $carteId;
                $tabId = $terrainId;
                $index = array_search($carteId, $terrainId);
                $tabIndex[] = $index;
                unset($terrain[$index]); // on retire du terrain
                $pioche = $partie->getDeck();
                if (count($pioche) > 0) {
                    $idcartep = array_pop($pioche);
                    $cartep = $carteRepository->find($idcartep);
                    if ($cartep !== null) {
                        $terrain[] = $cartep; //piocher et mettre sur le terrain
                    }
                }
                else
                {
                    $partie->setStatus(['status' => 'F']);
                }
                $joueur->setMain($main);
                $partie->setTerrain($terrain);
                $partie->setDeck($pioche);
                $entityManager->flush();
                return $this->json(['carteterrain' => $cartep->getJson(), 'cartemain' => $carte->getJson(),'index' => $tabIndex, 'Tabid' => $tabId,'carteId' => $cartu], 200);
            } else {
                return $this->json('erreur7', 500);
            }
        }
        else
        {
            $terrain = $partie->getTerrain();
            for($i=0;$i<count($cartes);$i++)
            {
                $carte = array_shift($cartes[$i]);
                $index = array_search($carte, $terrain);//todo:vérifier ça
                unset($terrain[$index]); //on retire
            }
            $pioche = $partie->getDeck();
            if (count($pioche) > 0) {
                for($i=0;$i<count($cartes);$i++)
                {
                    $idcartep = array_pop($pioche);
                    $cartep = $carteRepository->find($idcartep);
                    if ($cartep !== null) {
                        $terrain[] = $cartep; //piocher et mettre sur le terrain
                    }
                }
            }
            else
            {
                $partie->setStatus(['status' => 'F']);
            }
            $joueur->setChameaux($cartes);
            $partie->setTerrain($terrain);
            $partie->setDeck($pioche);
            $entityManager->flush();
            for($i=0;$i<count($cartes);$i++)
            {
                $carte = array_shift($cartes[$i]);
                $carteJson[] = $carte->getJson();
                $terrainJson[] = $terrain[$i]->getJson();
            }

            return $this->json(['carteterrain' => $terrain, 'cartechameau' => $carteJson], 200);
        }
        return $this->json('erreur', 500);
    }

    /**
     * @Route("/jouer-action/suivant/{partie}", name="jouer_action_suivant")
     */
    public function jouerActionSuivant( EntityManagerInterface $entityManager, Partie $partie)
    {
        if($partie->getMainJoueur() === true)//todo:Modifié parce qu'on ne stocke pas de "demi"-tour, calcul à faire sur pair/impair du nbManche au cas où
        {
            $partie->setMainJoueur(false);
        }
        else
        {
            $partie->setMainJoueur(true);
        }
        $partie->setStatus(['nbManche' => $partie->getStatus()['nbManche']+1]);
        $entityManager->flush();
        return $this->json('Joueur-suivant', 200);//todo:On utilise cette data ?
    }

    /**
     * @Route("/action/vendre/{partie}", name="action_vendre")
     */
    public function jouerActionVendre(EntityManagerInterface $entityManager,JetonRepository $jetonRepository, CarteRepository $carteRepository, Request $request, Partie $partie){
        $idcarte = $request->request->get('cartes');
        $carte = null;
        if(count($idcarte) > 1){
            for($i= 0 ; $i < count($idcarte); $i++)
            {
                $cartes[] = $carteRepository->find($idcarte[$i]);
            }
        }
        else
        {
            $carte = $carteRepository->find($idcarte[0]);
        }


        if ($this->getUser() == $partie->getJoueurs()[0]->getUsers()) //Condition de vérification du joueur
        {
            $joueur = $partie->getJoueurs()[0];
        }
        else{
            $joueur = $partie->getJoueurs()[1];
        }

    }


    /**
     * @Route("/liste-partie", name="partie_liste")
     */
    public function listePartie() {
    }

}
