<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tournaments")
 */
class TournamentController extends AbstractController
{
    /**
     * @Route("/", name="tournament_index")
     */
    public function index()
    {
        return $this->render('tournament/index.html.twig', [
            'controller_name' => 'TournamentController',
        ]);
    }

    /**
     * @Route("/classic", name="classic")
     */
    public function classic()
    {
        return $this->render('tournament/classic.html.twig');
    }

}
