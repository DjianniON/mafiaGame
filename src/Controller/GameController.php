<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Entity\Partie;
use App\Repository\CarteRepository;
use App\Repository\JetonRepository;
use App\Repository\PartieRepository;
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
    public function index(PartieRepository $partieRepository)
    {
        $games = $partieRepository->findAll();

        return $this->render('game/index.html.twig', [
            'controller_name' => 'GameController',
            'games' => $games
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
            $game->setStatus(['status' => 'T', 'nbManche' => 1, 'nbTour' => 1]);
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

            $game->setMainJoueur(true);//rand(0,1) for realtime games

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

            return $this->redirectToRoute('show_game', ['partie' => $game->getId()]);
        }
        $users = $userRepository->findAll();
        $user = $this->getUser();
        $nbUsers = count($users);

        for($i= 0; $i < $nbUsers; $i++)
        {
            if($users[$i]->getRoles()[0] === 'ROLE_BANNED')
            {
                $banned = $users[$i];
                $index = array_search($banned, $users);
                unset($users[$index]);
            }
            elseif($users[$i]->getUsername() === $user->getUsername())
            {
                $index = array_search($user, $users);
                unset($users[$index]);
            }

        }
        return $this->render('game/creer-partie.html.twig', [
            'joueurs' => $users
        ]);
    }

    /**
     * @Route("/show-game/{partie}", name="show_game")
     */
    public function afficherPartie(Partie $partie) {
        if($partie->getStatus()['status'] !== 'G')
        {
            return $this->render('game/afficher_partie.html.twig', ['partie' => $partie]);
        }
        return $this->redirectToRoute('partie_finie',['partie' => $partie->getId()]);

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
    public function actualisePlateau(Partie $partie, JetonRepository $jetonRepository, CarteRepository $carteRepository, EntityManagerInterface $entityManager) {
        $p1 = $partie->getJoueurs()[0];
        $p2 = $partie->getJoueurs()[1];
        if($p1->getScore() === 2 || $p2->getScore() === 2 || $partie->getStatus()['status'] === 'G')
        {
            $partie->setStatus(['status' => 'G', 'nbManche' => $partie->getStatus()['nbManche'], 'nbTour' => $partie->getStatus()['nbTour']]);
            if($p1->getScore() > $p2->getScore())
            {
                $user1 = $p1->getUsers();
                $user1->setNbVictoires($user1->getNbVictoires()+1);
                $this->GenerateElo($user1,$partie);
            }
            else
            {
                $user1 = $p1->getUsers();
                $user1->setNbVictoires($user1->getNbVictoires()+1);
                $this->GenerateElo($user1,$partie);
            }
            $entityManager->flush();
            return $this->json($this->generateUrl('partie_finie',['partie' => $partie->getId()]));
        }
        if($partie->getStatus()['status'] !== 'F') {
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
        else//todo:scoring
        {
            $p1Jetons = $p1->getTasJetons();
            $p2Jetons = $p2->getTasJetons();
            $p1Merch = 0;
            $p2Merch = 0;
            $p1Chap = 0;
            $p2Chap = 0;
            $p1Total = 0;
            $p2Total = 0;

            if(count($p1->getChameaux()) > count($p2->getChameaux()))
            {
                $p1Total += 5;
            }
            else
            {
                $p2Total += 5;
            }

            for($i=0; $i < count($p1Jetons);$i++)
            {
                $p1Total += $p1Jetons[$i]->getValeur();
                if($p1Jetons[$i]->getType()->getNom() === ('Chap_3' || 'Chap_4' || 'Chap_5'))
                {
                    $p1Chap += 1;
                }
                else
                {
                    $p1Merch += 1;
                }
            }
            for($i=0; $i < count($p2Jetons);$i++)
            {
                $p2Total += $p2Jetons[$i]->getValeur();
                if($p2Jetons[$i]->getType()->getNom() === ('Chap_3' || 'Chap_4' || 'Chap_5'))//todo:ça fonctionne cette écriture ?
                {
                    $p2Chap += 1;
                }
                else
                {
                    $p2Merch += 1;
                }
            }
            if($p1Total > $p2Total)
            {
                $score = $p1->getScore() + 1;
                $p1->setScore($score);
            }
            elseif($p1Total === $p2Total)
            {
                if($p1Chap > $p2Chap)
                {
                    $score = $p1->getScore() + 1;
                    $p1->setScore($score);
                }
                elseif($p1Chap === $p2Chap)
                {
                    if($p1Merch > $p2Merch)
                    {
                        $score = $p1->getScore() + 1;
                        $p1->setScore($score);
                    }
                    else
                    {
                        $score = $p2->getScore() + 1;
                        $p2->setScore($score);
                    }
                }
                else
                {
                    $score = $p2->getScore() + 1;
                    $p2->setScore($score);
                }
            }
            else
            {
                $score = $p2->getScore() + 1;
                $p2->setScore($score);
            }
             $entityManager->flush();

            if($p1->getScore() === 2 || $p2->getScore() === 2)
            {
                return $this->json($this->generateUrl('partie_finie',['partie' => $partie->getId()]));
            }
            else//Génération PARTIE MANCHE + 1
            {
                $partie->setStatus(['status' => 'T', 'nbManche' => $partie->getStatus()['nbManche'] + 1, 'nbTour' => 1]);
                $partie->setDefausse(false);


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
                $partie->setTerrain($tTerrain);

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
                $p1->setMain($tMain);
                $p1->setChameaux($tChameau);
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

                $p2->setMain($tMain);
                $p2->setChameaux($tChameau);
                $p2->setTasJetons([]);

                $partie->setMainJoueur(true);//todo:loser

                $tDeck = [];
                $nbCartes = count($cartes);
                for($i=0; $i<$nbCartes; $i++) {
                    $tDeck[] = array_pop($cartes);
                }
                $partie->setDeck($tDeck);

                $jetons = $jetonRepository->findByTypeValue();
                $partie->setTasJeton($jetons);


                $em->flush();
                return $this->json('good',200);
            }
        }
        return $this->json('erreur',500);
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


        if ($cartes !== null && count($cartes) === 1 && $cartes[0]->getType()->getNom() !== 'Cadillac') {//Vérification cas 1 carte marchandise
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
                }
                $index = array_search($carteId, $terrainId);
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
                    $partie->setStatus(['status' => 'F', 'nbManche' => $partie->getStatus()['nbManche'], 'nbTour' => $partie->getStatus()['nbTour']]);
                }
                $joueur->setMain($main);
                $partie->setTerrain($terrain);
                $partie->setDeck($pioche);
                $entityManager->flush();
                return $this->json(['carteterrain' => $cartep->getJson(), 'cartemain' => $carte->getJson()], 200);
            } else {
                return $this->json('erreur7', 500);
            }
        }
        else //Traitement pour les chameaux
        {
            $chameaux = $joueur->getChameaux();
            $terrain = array_values($partie->getTerrain());//lalignemagique
            for($i=0;$i < count($terrain);$i++)
            {
                $terrainId[] = $terrain[$i]->getId();
            }
            for($i=0;$i<count($cartes);$i++)
            {
                $carteId = $cartes[$i]->getId();
                $chameaux[] = $cartes[$i];//On ajoute chaque carte Cadillac
                $index = array_search($carteId, $terrainId);//todo:vérifier ça
                unset($terrain[$index]); //on retire
            }
            $pioche = $partie->getDeck();
            if (count($pioche) > 0) {
                for($i=0;$i<count($cartes);$i++)
                {
                    $idcartep = array_pop($pioche);
                    $cartep = $carteRepository->find($idcartep);
                    $terrainp[] = $cartep;
                    if ($cartep !== null) {
                        $terrain[] = $cartep; //piocher et mettre sur le terrain
                    }
                }
            }
            else
            {
                $partie->setStatus(['status' => 'F', 'nbManche' => $partie->getStatus()['nbManche'], 'nbTour' => $partie->getStatus()['nbTour']]);
            }
            $joueur->setChameaux($chameaux);
            $partie->setTerrain($terrain);
            $partie->setDeck($pioche);
            $entityManager->flush();
            for($i=0;$i<count($cartes);$i++)
            {
                $carte = $cartes[$i];
                $carteJson[] = $carte->getJson();
                $terrainJson[] = $terrainp[$i]->getJson();
            }

            return $this->json(['carteterrain' => $terrainJson,'cartechameau' => $carteJson], 200);
        }
        return $this->json('erreur', 500);
    }

    /**
     * @Route("/jouer-action/suivant/{partie}", name="jouer_action_suivant")
     */
    public function jouerActionSuivant( EntityManagerInterface $entityManager, Partie $partie)
    {
        if($partie->getMainJoueur() === true)//todo:Modifié parce qu'on ne stocke pas de "demi"-tour, calcul à faire sur pair/impair du nbTour au cas où
        {
            $partie->setMainJoueur(false);
        }
        else
        {
            $partie->setMainJoueur(true);
        }

        $partie->setStatus(['status' => $partie->getStatus()['status'], 'nbManche' => $partie->getStatus()['nbManche'],'nbTour' => $partie->getStatus()['nbTour']+1]);
        $entityManager->flush();
        return $this->json('Joueur-suivant', 200);//todo:On utilise cette data ? Ou juste le return obligatoire ?
    }

    /**
     * @Route("/action/vendre/{partie}", name="action_vendre")
     */
    public function jouerActionVendre(EntityManagerInterface $entityManager, CarteRepository $carteRepository, Request $request, Partie $partie){
        $idcarte = $request->request->get('cartes');
        $alljetons = $partie->getTasJeton();
        $tabVide = 0;
        $verifTabJetons = array_values($partie->getTasJeton());//lalignemagique

        for($i = 0 ; $i < count($verifTabJetons); $i++)
        {
            if(count($verifTabJetons[$i]) === 0)
            {
                $tabVide += 1;
            }
        }

        if ($this->getUser() == $partie->getJoueurs()[0]->getUsers()) //Condition de vérification du joueur
        {
            $joueur = $partie->getJoueurs()[0];
        } else {
            $joueur = $partie->getJoueurs()[1];
        }

        $mainJoueur = array_values($joueur->getMain());

        if($tabVide < 3) {
            for ($i = 0; $i < count($idcarte); $i++) {
                $cartes[] = $carteRepository->find($idcarte[$i]);
            }

            $jetonsJoueur = $joueur->getTasJetons();
            $nbCartes = count($cartes);

            if ($nbCartes === 3 && count($alljetons['Chap_3']) !== 0) {
                $jeton = array_pop($alljetons['Chap_3']);
                $jetonsJoueur[] = $jeton;
                $jetonTab[] = $jeton;
            }

            elseif ($nbCartes === 4 && count($alljetons['Chap_4']) !== 0) {
                $jeton = array_pop($alljetons['Chap_4']);
                $jetonsJoueur[] = $jeton;
                $jetonTab[] = $jeton;
            }

            elseif ($nbCartes >= 5 && count($alljetons['Chap_5']) !== 0) {
                $jeton = array_pop($alljetons['Chap_5']);
                $jetonsJoueur[] = $jeton;
                $jetonTab[] = $jeton;
            }


            if(count($alljetons[$cartes[0]->getType()->getNom()]) === $nbCartes - 1)//Cas où il ne reste qu'un jeton
                {
                    for($i = 0; $i < count($alljetons[$cartes[0]->getType()->getNom()]); $i++)
                    {
                        $jeton = array_pop($alljetons[$cartes[0]->getType()->getNom()]);
                        $jetonsJoueur[] = $jeton;
                    }
                }
                elseif($cartes[0]->getType()->getNom() === 'Arme' || $cartes[0]->getType()->getNom() === 'Drogue' || $cartes[0]->getType()->getNom() === 'Oeuvre')//Cas des meilleures marchandises
                {
                    if(count($alljetons[$cartes[0]->getType()->getNom()]) <= 1)//S'il ne reste qu'un jeton
                        {
                            $jeton = array_pop($alljetons[$cartes[0]->getType()->getNom()]);
                            $jetonsJoueur[] = $jeton;
                            $jetonTab[] = $jeton;
                        }
                        else
                        {
                            for ($i = 0; $i+1 < $nbCartes; $i++)
                            {
                                $jeton = array_pop($alljetons[$cartes[0]->getType()->getNom()]);
                                $jetonsJoueur[] = $jeton;
                                $jetonTab[] = $jeton;
                            }
                        }
                    }
                    elseif(count($alljetons[$cartes[0]->getType()->getNom()]) < $nbCartes)
                    {
                        for($i = 0; $i < count($alljetons[$cartes[0]->getType()->getNom()]); $i++)//todo:wip
                        {
                            $jeton = array_pop($alljetons[$cartes[0]->getType()->getNom()]);
                            $jetonsJoueur[] = $jeton;
                            $jetonTab[] = $jeton;
                        }
                    }
                    else//le reste
                    {
                        for ($i = 0; $i < $nbCartes; $i++)
                        {
                            $jeton = array_pop($alljetons[$cartes[0]->getType()->getNom()]);
                            $jetonsJoueur[] = $jeton;
                            $jetonTab[] = $jeton;
                        }
                    }

                    $joueur->setTasJetons($jetonsJoueur);

            for($i=0;$i<count($jetonTab);$i++)
            {
                $token = $jetonTab[$i];
                $jetonJson[] = $token->getId();
            }
            for($i=0;$i<count($mainJoueur);$i++)
            {
                $mainId[] = $mainJoueur[$i]->getId();
            }
            for($i=0;$i<$nbCartes;$i++)
            {
                $carteId = $cartes[$i]->getId();
                $mainJson[] = $carteId;
                $index = array_search($carteId, $mainId);//todo:vérifier ça
                unset($mainJoueur[$index]); //on retire
            }
            $joueur->setMain($mainJoueur);
            $partie->setTasJeton($alljetons);
            $entityManager->flush();
            return $this->json(['jetons' => $jetonJson, 'cartemain' => $mainJson], 200);
        }
        else
        {
            $partie->setStatus(['status' => 'F', 'nbManche' => $partie->getStatus()['nbManche'], 'nbTour' => $partie->getStatus()['nbTour']]);
            $entityManager->flush();
            return $this->json('fin Manche', 200);
        }
        return $this->json('erreur', 500);
    }

    /**
     * @Route("/action-trade/{partie}", name="action_trade")
     */
    public function jouerActionTrade(EntityManagerInterface $entityManager, CarteRepository $carteRepository, Request $request, Partie $partie)
    {
        $idcarte = $request->request->get('cartes');
        $idboard = $request->request->get('terrain');
        $idcadillac = $request->request->get('cadillac');

    if($idboard !== null) {
        for ($i = 0; $i < count($idboard); $i++) {
            $board[] = $carteRepository->find($idboard[$i]);
        }
        if($idcarte !== null)
        {
            for($i = 0; $i < count($idcarte); $i++)
            {
                $cartes[] = $carteRepository->find($idcarte[$i]);
            }
        }
        if($idcadillac !== null)
        {
            for($i = 0; $i < count($idcadillac); $i++)
            {
                $chameaux[] = $carteRepository->find($idcadillac[$i]);
            }
        }

        if ($this->getUser() == $partie->getJoueurs()[0]->getUsers()) //Condition de vérification du joueur
        {
            $joueur = $partie->getJoueurs()[0];
        } else {
            $joueur = $partie->getJoueurs()[1];
        }

        $terrain = array_values($partie->getTerrain());//lalignemagique
        $main = array_values($joueur->getMain());
        $cadillac = array_values($joueur->getChameaux());

        for ($i = 0; $i < count($terrain); $i++) {
            $terrainId[] = $terrain[$i]->getId();
        }
        if($idcadillac !== null)
        {
            for ($i = 0; $i < count($cadillac); $i++) {
                $cadId[] = $cadillac[$i]->getId();
            }
        }
        if($idcarte !== null)
        {
            for ($i = 0; $i < count($main); $i++) {
                $mainId[] = $main[$i]->getId();
            }
        }
        for ($i = 0; $i < count($board); $i++) {
            $main[] = $board[$i];
            $carteId = $board[$i]->getId();
            $boardJson[] = $board[$i]->getJson();
            $index = array_search($carteId, $terrainId);
            unset($terrain[$index]);
        }
        if($idcadillac !== null)
        {
            for ($i = 0; $i < count($chameaux); $i++)//vide les chameaux et les set sur terrain
            {
                $terrain[] = $chameaux[$i];
                $carteId = $chameaux[$i]->getId();
                $chamJson[] = $chameaux[$i]->getJson();
                $index = array_search($carteId, $cadId);
                unset($cadillac[$index]);
            }
        }

        if($idcarte !== null) {
            for ($i = 0; $i < count($cartes); $i++) {
                $terrain[] = $cartes[$i];
                $carteId = $cartes[$i]->getId();
                $mainJson[] = $cartes[$i]->getJson();
                $index = array_search($carteId, $mainId);
                unset($main[$index]);
            }
        }
        $partie->setTerrain($terrain);
        $joueur->setMain($main);
        $joueur->setChameaux($cadillac);
        $entityManager->flush();

        if($idcadillac !== null && $idcarte !== null)
        {
            return $this->json(['carteterrain' => $boardJson, 'cartechameau' => $chamJson, 'cartemain' => $mainJson], 200);

        }
        elseif($idcarte === null)
        {
            return $this->json(['carteterrain' => $boardJson, 'cartechameau' => $chamJson], 200);
        }
        else
        {
            return $this->json(['carteterrain' => $boardJson, 'cartemain' => $mainJson], 200);

        }
    }
    return $this->json('erreur', 500);
    }

    /**
     * @Route("/game-finished/{partie}", name="partie_finie")
     */
    public function partieFinie(Partie $partie)
    {
        if($partie->getStatus()['status'] === 'G')
        {
            $p1 = $partie->getJoueurs()[0];
            $p2 = $partie->getJoueurs()[1];
            if($p1->getScore() > $p2->getScore())
            {
                $player = $p1;
            }
            else
            {
                $player = $p2;
            }
            return $this->render('game/victory.html.twig',['player' => $player]);
        }
        else
        {
            return $this->render('game/error.html.twig');
        }
    }

    private function GenerateElo(\App\Entity\User $user1, Partie $partie)
    {
        $elop1 = $user1->getElo();
        if($user1 === $partie->getJoueurs()[0])
        {
            $score = $partie->getJoueurs()[1]->getScore();
            $user2 = $partie->getJoueurs()[1]->getUsers();
        }
        else
        {
            $score = $partie->getJoueurs()[0]->getScore();
            $user2 = $partie->getJoueurs()[0]->getUsers();
        }
        if($score === 0)
        {
            $user1->setElo($elop1 + 40);

            $user2->setElo($user2->getElo() - 20);
        }
        else
        {
            $user1->setElo($elop1 + 20);
            $user2->setElo($user2->getElo() - 10);
        }
        if($user2->getElo() <= 0)
        {
            $user2->setElo(0);
        }
        $em = $this->getDoctrine()->getManager();
        $em->flush();
    }


}
