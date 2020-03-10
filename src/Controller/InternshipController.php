<?php

namespace App\Controller;

use App\Entity\Internship;
use App\Entity\SearchInternship;
use App\Form\InternshipType;
use App\Form\SearchInternshipType;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class InternshipController extends AbstractController
{
    /**
     * @Route("/stages", name="internships")
     * @Security("is_granted('ROLE_USER')")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $search = new SearchInternship();
        $form = $this->createForm(SearchInternshipType::class, $search);
        $form->handleRequest($request);

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('i')
            ->from('App:Internship', 'i')
            ->andwhere('i.visible = 1');

        if($search->getCategory())
        {
            $qb = $qb->andWhere('i.category = :category')
                ->setParameter('category', '%'.$search->getCategory().'%');
        }

        if($search->getTitle())
        {
            $qb = $qb->andWhere('i.title LIKE :title')
                ->setParameter('title', '%'.$search->getTitle().'%');
        }

        if($search->getCompany())
        {
            $qb = $qb->andWhere('i.company LIKE :company')
                ->setParameter('company', '%'.$search->getCompany().'%');
        }

        if($search->getCountry())
        {
            $qb = $qb->andWhere('i.country LIKE :country')
                ->setParameter('country', '%'.$search->getCountry().'%');
        }

        if($search->getCity())
        {
            $qb = $qb->andWhere('i.city LIKE :city')
                ->setParameter('city', '%'.$search->getCity().'%');
        }

        if($search->getDuration())
        {
            $qb = $qb->andWhere('i.duration LIKE :duration')
                ->setParameter('duration', '%'.$search->getDuration().'%');
        }

        $query = $qb->getQuery()->getResult();

        $stages = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('internship/index.html.twig', [
            'stages' => $stages,
            'form' => $form->createView()]);
    }

    /**
     * @Route("/mes_stages", name="internships_my")
     * @Security("is_granted('ROLE_USER')")
     */
    public function internships()
    {
        $user = $this->getUser();
        $stages = $this->getDoctrine()->getRepository(Internship::class)->findBy(['author' => $user]);

        return $this->render('internship/internships.html.twig', ['stages' => $stages]);
    }

    /**
     * @Route("/stage/{id}", name="internship")
     * @Security("is_granted('ROLE_USER')")
     */
    public function stage($id, PaginatorInterface $paginator, Request $request)
    {
        $stage = $this->getDoctrine()->getRepository(Internship::class)->find($id);

        if(!$this->isGranted('view', $stage))
        {
            $this->addFlash('danger','Vous ne pouvez pas voir ce stage');
            return $this->redirectToRoute('internships');
        }

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('i')
            ->from('App:Internship', 'i')
            ->where('i.id != :id AND i.visible = 1 AND i.category = :category AND i.duration = :duration')
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
     * @Security("is_granted('ROLE_USER')")
     */
    public function new(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $internship = new Internship();
        $form = $this->createForm(InternshipType::class, $internship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $internship->setAuthor($this->getUser())
                ->setDuration()
                ->setAddedOn(new \DateTime())
                ->setVisible(False);

            $entityManager->persist($internship);
            $entityManager->flush();

            $this->addFlash('success','Votre stage a bien été ajouté');
            return $this->redirectToRoute('internships_my');
        }

        return $this->render('internship/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="internship_edit")
     * @Security("is_granted('ROLE_USER')")
     */
    public function edit($id, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $internship = $this->getDoctrine()->getRepository(Internship::class)->find($id);

        if(!$this->isGranted('edit', $internship))
        {
            $this->addFlash('danger','Vous ne pouvez pas modifier ce stage');
            return $this->redirectToRoute('internships');
        }

        $form = $this->createForm(InternshipType::class, $internship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $internship->setVisible(False);

            $entityManager->persist($internship);
            $entityManager->flush();

            $this->addFlash('success','Votre stage a bien été modifié');
            return $this->redirectToRoute('internships_my');
        }

        return $this->render('internship/edit.html.twig', [
            'form' => $form->createView(),
            'stage' => $internship
        ]);
    }
}