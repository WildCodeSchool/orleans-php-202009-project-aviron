<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Subscriber;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/subscribers", name="subscribers_")
 */
class SubscriberController extends AbstractController
{
    /**
     * Correspond à la route /subscribers/ et au name "subscriber_index"
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $subscribers = $this->getDoctrine()
            ->getRepository(Subscriber::class)
            ->findAll();
        $seasons = $this->getDoctrine()
            ->getRepository(Season::class)
            ->findAll();

        return $this->render('subscriber/index.html.twig', [
            'subscribers' => $subscribers,
            'seasons' => $seasons,
        ]);
    }
}
