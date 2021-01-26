<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
        /**
         * @Route("profil", name="profile")
         */
    public function profile(): Response
    {
        return $this->render('home/profile.html.twig');
    }
}
