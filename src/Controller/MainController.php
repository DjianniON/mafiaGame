<?php
namespace App\Controller;

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
    public function lader()
    {
        return $this->render('ladder.html.twig');
    }
}