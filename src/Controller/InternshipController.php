<?php

namespace App\Controller;

use App\Entity\Internship;
use App\Form\InternshipType;

use Knp\Component\Pager\PaginatorInterface;
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
    public function index(PaginatorInterface $paginator, Request $request)
    {
        if(!$this->security->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $stages = $paginator->paginate(
            $this->getDoctrine()->getRepository(Internship::class)->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('internship/index.html.twig', ['stages' => $stages]);
    }

    /**
     * @Route("/mes_stages", name="internships_my")
     */
    public function internships()
    {
        if(!$this->security->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $user = $this->getUser();
        $stages = $this->getDoctrine()->getRepository(Internship::class)->findBy(['author' => $user]);

        return $this->render('internship/internships.html.twig', ['stages' => $stages]);
    }

    /**
     * @Route("/stage/{id}", name="internship")
     */
    public function stage($id, PaginatorInterface $paginator, Request $request)
    {
        if(!$this->security->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('home');
        }

        $stage = $this->getDoctrine()->getRepository(Internship::class)->find($id);

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();

        $qb->select('i')
            ->from('App:Internship', 'i')
            ->where('i.id != :id AND i.category = :category AND i.duration = :duration')
            ->setParameter('id', $id)
            ->setParameter('category', $stage->getCategory())
            ->setParameter('duration', $stage->getDuration());
        $query = $qb->getQuery();

        $stages = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('internship/internship.html.twig', [
            'stage' => $stage,
            'stages' => $stages
            ]);
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
            $internship->setAuthor($this->getUser())
                        ->setDuration();

            $entityManager->persist($internship);
            $entityManager->flush();

            return $this->redirectToRoute('internships_my');
        }

        return $this->render('internship/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
