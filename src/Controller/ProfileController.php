<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    public function profile(): Response
    {
        return $this->render('home/profile.html.twig');
    }
}
