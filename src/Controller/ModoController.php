<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Internship;
use App\Entity\User;
use App\Form\CategoryType;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ModoController
 * @Route("/moderation")
 * @Security("is_granted('ROLE_MODO')")
 */
class ModoController extends AbstractController
{
    /**
     * @Route("/", name="moderation")
     */
    public function index(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success','Votre catégorie a bien été créée');
        }

        return $this->render('modo/index.html.twig', [
            'form' => $form->createView(),
            'nb_internships' => $this->getDoctrine()->getRepository(Internship::class)->getNbInvisibleInternships(),
            'nb_users' => $this->getDoctrine()->getRepository(User::class)->getNbUser(),
            'nb_categories' => $this->getDoctrine()->getRepository(Category::class)->getNbCategory()
        ]);
    }

    /**
     * @Route("/stages", name="internships_validation")
     */
    public function modify(PaginatorInterface $paginator, Request $request)
    {
        $stages = $paginator->paginate(
            $this->getDoctrine()->getRepository(Internship::class)->getInvisible(), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('modo/internships.html.twig', [
            'stages' => $stages
        ]);
    }

    /**
     * @Route("/internship/{id}/validate", name="internship_validate")
     */
    public function validate(Internship $internship, \Swift_Mailer $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $internship->setVisible(True)
            ->setPublishedOn(new \DateTime());

        $entityManager->persist($internship);
        $entityManager->flush();

        $this->addFlash('success',"Le stage {$internship->getTitle()} a bien été validé");

        $user = $internship->getAuthor();

        $message = (new \Swift_Message("[BDD Stage] Validation de votre stage {$internship->getTitle()}"))
            ->setFrom('no-reply@bdd-stage.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'modo/mail.html.twig', [
                        'user' => $user,
                        'internship' => $internship,
                        'action' => 'validé'
                    ]),
                'text/html'
            );

        $mailer->send($message);

        return $this->redirectToRoute('internships_validation');
    }

    /**
     * @Route("/internship/{id}/reject", name="internship_reject")
     */
    public function reject(Internship $internship, \Swift_Mailer $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($internship);
        $entityManager->flush();

        $this->addFlash('success',"Le stage {$internship->getTitle()} a bien été supprimé");

        $user = $internship->getAuthor();

        $message = (new \Swift_Message("[BDD Stage] Suppression de votre stage {$internship->getTitle()}"))
            ->setFrom('no-reply@bdd-stage.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'modo/mail.html.twig', [
                    'user' => $user,
                    'internship' => $internship,
                    'action' => 'rejeté'
                ]),
                'text/html'
            );

        $mailer->send($message);

        return $this->redirectToRoute('internships_validation');
    }
}