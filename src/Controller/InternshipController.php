<?php

namespace App\Controller;

use App\Entity\Internship;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InternshipController extends AbstractController
{
    /**
     * @Route("/stages", name="internships")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Internship::class);
        $stages = $repo->findAll();
        return $this->render('internship/index.html.twig', ['stages' => $stages]);
    }

    /**
     * @Route("/stage/{id}", name="internship")
     */
    public function stage($id)
    {
        $stage = $this->getDoctrine()->getRepository(Internship::class)->find($id);
        return $this->render('internship/internship.html.twig', ['stage' => $stage]);
    }
}