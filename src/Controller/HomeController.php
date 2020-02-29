<?php

namespace App\Controller;

use App\Entity\Internship;
use App\Entity\User;
use App\Form\RegistrationType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="home", methods={"GET", "POST"})
     */
    public function home(AuthenticationUtils $authenticationUtils)
    {
        if(!$this->security->isGranted('ROLE_USER')) {
            $error = $authenticationUtils->getLastAuthenticationError();

            $lastUsername = $authenticationUtils->getLastUsername();
            return $this->render('home/login.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]);
        }

        $repo = $this->getDoctrine()->getRepository(Internship::class);

        return $this->render('home/index.html.twig', [
            'nb_internships' => $repo->getNbInternships(),
            'nb_countries' => $repo->getNbCountry(),
            'nb_cities' => $repo->getNbCity(),
            'stages' => $repo->findInternshipByDate(10)
        ]);
    }

    /**
     * @Route("/deconnexion", name="logout")
     */
    public function logout(){}

    /**
     * @Route("/inscription", name="security_registration")
     */
    public function registration(Request $request, UserPasswordEncoderInterface $encoder)
    {
        if($this->security->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('internships');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash)
                ->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('home/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
