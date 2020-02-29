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
 * Class AdminController
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
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

        return $this->render('admin/index.html.twig', [
            'form' => $form->createView(),
            'nb_users' => $this->getDoctrine()->getRepository(User::class)->getNbUser(),
            'nb_categories' => $this->getDoctrine()->getRepository(Category::class)->getNbCategory()
        ]);
    }

    /**
     * @Route("/user", name="user")
     */
    public function user(PaginatorInterface $paginator, Request $request)
    {
        $search = new SearchUser();
        $form = $this->createForm(SearchUserType::class, $search);
        $form->handleRequest($request);

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('App:User', 'u');

        if($search->getUserName())
        {
            $qb = $qb->andWhere('u.userName LIKE :username')
                ->setParameter('username', '%'.$search->getUserName().'%');
        }

        if($search->getFirstName())
        {
            $qb = $qb->andWhere('u.firstName LIKE :firstname')
                ->setParameter('firstname', '%'.$search->getFirstName().'%');
        }

        if($search->getLastName())
        {
            $qb = $qb->andWhere('i.lastName LIKE :lastname')
                ->setParameter('lastname', '%'.$search->getLastName().'%');
        }

        $query = $qb->getQuery()->getResult();

        $users = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );

        return $this->render('admin/user.html.twig', [
            'users' => $users,
            'form' => $form->createView()]);
    }

    /**
     * @Route("/promote/{id}", name="promote")
     */
    public function promote($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setRoles(['ROLE_ADMIN']);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', "L 'utilisateur {$user->getUserName()} a bien été promu admin");
        return $this->redirectToRoute('user');
    }

    /**
     * @Route("/destitute/{id}", name="destitute")
     */
    public function destitute($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $user->setRoles(['ROLE_USER']);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', "L 'utilisateur {$user->getUserName()} a bien été destitué en utilisateur lambda");
        return $this->redirectToRoute('user');
    }
}