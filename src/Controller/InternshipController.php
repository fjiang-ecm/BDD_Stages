<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InternshipController extends AbstractController
{
    /**
     * @Route("/stages", name="internship")
     */
    public function index()
    {
        return $this->render('internship/index.html.twig', [
            'controller_name' => 'InternshipController',
        ]);
    }
}
