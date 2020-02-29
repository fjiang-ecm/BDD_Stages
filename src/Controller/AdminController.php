<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'nb_users' => $this->getDoctrine()->getRepository(User::class)->getNbUser(),
            'nb_categories' => $this->getDoctrine()->getRepository(Category::class)->getNbCategory()
        ]);
    }
}