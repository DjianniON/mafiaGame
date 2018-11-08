<?php
namespace App\Controller;

use App\Repository\JoueurRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/admin", name="admin_index")
     */
    public function admin()
    {
        return $this->render('Admin/index.html.twig');
    }

    /**
     * @Route("/story", name="story")
     */
    public function story()
    {
        return $this->render('story.html.twig');
    }


    /**
     * @Route("/rules", name="rules")
     */
    public function rules()
    {
        return $this->render('rules.html.twig');
    }

    /**
     * @Route("/ladder", name="ladder")
     */
    public function ladder(UserRepository $userRepository)
    {
        $joueurs = $userRepository->findByScore();
        for($i = 0; $i < count($joueurs); $i++)
        {
            if($joueurs[$i]->getRoles()[0] === 'ROLE_BANNED')
            {
                $banned = $joueurs[$i];
                $index = array_search($banned, $joueurs);
                unset($joueurs[$index]);
            }
        }
        return $this->render('ladder.html.twig',['joueurs' => $joueurs]);
    }
}