<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Entity\Partie;
use App\Repository\CarteRepository;
use App\Repository\JetonRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    /**
     * @Route("/game", name="game_index")
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
            //$game->setJoueur1($p1);//todo: Remplir correctement l'entité
            $game->setDate(new \DateTime('now'));

            $game->setHistoriquePartie(['nbTour' => 1]);
            $game->setStatus(['state' => 'true', 'nbManche' => '1']);
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
                if($carte->getType()->getNom() === 'Chameau'){
                    $tChameau[] = $carte;
                }
                else{
                    $tMain[] = $carte;
                }
            }
            $em = $this->getDoctrine()->getManager();

            $p1 = new Joueur();
            $p1->setUsers($userRepository->find($request->request->get('player1')));
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

            $game->setMainJoueur(0);

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
        switch ($partie->getStatus()) {
            //tester si je suis J1 ou J2 et en fonction adapter les return.
            case '1':
                return $this->json('montour');
            case '2':
                return $this->json('touradversaire');
            case 'T':
                return $this->json('T');
            default:
                return $this->json('E');
        }
    }

    /**
     * @Route("/liste-partie", name="partie_liste")
     */
    public function listePartie() {
    }

}
