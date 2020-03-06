<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\SearchUser;
use App\Entity\User;
use App\Form\CategoryType;
use App\Form\SearchUserType;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ModoController
 * @Security("is_granted('ROLE_MODO')")
 */
class ModoController extends AbstractController
{
    /**
     * @Route("/moderation", name="moderation")
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

        return $this->render('modo/index.html.twig', [
            'form' => $form->createView(),
            'nb_users' => $this->getDoctrine()->getRepository(User::class)->getNbUser(),
            'nb_categories' => $this->getDoctrine()->getRepository(Category::class)->getNbCategory()
        ]);
    }
}