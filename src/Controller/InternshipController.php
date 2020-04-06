<?php

namespace App\Controller;

use App\Entity\Internship;
use App\Entity\SearchInternship;
use App\Form\InternshipType;
use App\Form\SearchInternshipType;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Security("is_granted('view', stage)")
     */
    public function stage(Internship $stage, PaginatorInterface $paginator, Request $request)
    {
        $stages = $paginator->paginate(
            $this->getDoctrine()->getRepository(Internship::class)->getAlike($stage),
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('internship/internship.html.twig', [
            'stage' => $stage,
            'stages' => $stages
            ]);
    }

    /**
     * @Route("/internship/new", name="internship_new")
     * @Security("is_granted('ROLE_USER')")
     */
    public function internshipNew(Request $request)
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
     * @Route("/internship/{id}/edit", name="internship_edit")
     * @Security("is_granted('edit', internship)")
     */
    public function internshipEdit(Internship $internship, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(InternshipType::class, $internship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (!$this->isGranted('ROLE_MODO')) {
                $internship->setVisible(False);
            }

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

    /**
     * @Route("/internship/{id}/remove", name="internship_remove", methods={"POST"})
     * @Security("is_granted('edit', internship)")
     */
    public function internshipRemove(Internship $internship, Request $request)
    {
        if($request->request->get('token') && $this->isCsrfTokenValid('remove', $request->request->get('token'))){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($internship);
            $entityManager->flush();

            if($this->getUser() == $internship->getAuthor()) {
                $this->addFlash('success', 'Votre stage a bien été supprimé');
                return $this->redirectToRoute('home');
            }
            else {
                $this->addFlash('success', "Le stage {$internship->getTitle()} a bien été supprimé");
                return $this->redirectToRoute('internships_validation');
            }
        }

        return new JsonResponse(['error' => 'Invalid csrf token.'], 403);
    }
}