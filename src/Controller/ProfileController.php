<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    public const ROLES = [
        'ROLE_ADMIN' => "Administateur",
        'ROLE_USER' => "Utilisateur",
    ];
        /**
         * @Route("profile", name="profile")
         */
    public function profile(): Response
    {
        return $this->render('home/profile.html.twig');
    }
}
