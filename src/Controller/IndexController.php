<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\TripsService;


class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(TripsService $tripsService)
    {
        $tripsService->calculateTrips();
        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
        ]);
    }
}
