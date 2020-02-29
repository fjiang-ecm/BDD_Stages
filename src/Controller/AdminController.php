<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Internship;
use App\Entity\User;
use App\Form\CategoryType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @Route("/admin")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
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
            return $this->redirectToRoute('admin');
        }

        $repo = $this->getDoctrine()->getRepository(Internship::class);

        return $this->render('admin/index.html.twig', [
            'form' => $form->createView(),
            'nb_users' => $this->getDoctrine()->getRepository(User::class)->getNbUser(),
            'nb_categories' => $this->getDoctrine()->getRepository(Category::class)->getNbCategory()
        ]);
    }
}