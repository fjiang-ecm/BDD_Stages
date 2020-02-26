<?php

namespace App\Controller;

use App\Entity\Internship;
use App\Form\InternshipType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class InternshipController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/stages", name="internships")
     */
    public function index()
    {
        if(!$this->security->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $repo = $this->getDoctrine()->getRepository(Internship::class);
        $stages = $repo->findAll();
        return $this->render('internship/index.html.twig', ['stages' => $stages]);
    }

    /**
     * @Route("/stage/{id}", name="internship")
     */
    public function stage($id)
    {
        if(!$this->security->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $stage = $this->getDoctrine()->getRepository(Internship::class)->find($id);
        return $this->render('internship/internship.html.twig', ['stage' => $stage]);
    }

    /**
     * @Route("/new", name="internship_new")
     */
    public function registration(Request $request)
    {
        if(!$this->security->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $internship = new Internship();

        $form = $this->createForm(InternshipType::class, $internship);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $internship->setAuthor($this->getUser());

            $entityManager->persist($internship);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('internship/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
