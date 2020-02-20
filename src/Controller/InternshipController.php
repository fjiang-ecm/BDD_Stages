<?php

namespace App\Controller;

use App\Entity\Internship;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InternshipController extends AbstractController
{
    /**
     * @Route("/stages", name="internship")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Internship::class);
        $stages = $repo->findAll();
        return $this->render('internship/index.html.twig', ['stages' => $stages]);
    }
}