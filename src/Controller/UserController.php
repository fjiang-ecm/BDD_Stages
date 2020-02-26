<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class UserController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/login", name="user_login")
     */
    public function index(Request $request, TokenStorageInterface $tokenStorage, SessionInterface $session, EventDispatcherInterface $dispatcher)
    {
        if($this->security->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('internships');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $id = $this->getParameter('oauth_id');
        $secret = $this->getParameter('oauth_secret');
        $base = $this->getParameter('oauth_base');

        $client = new \OAuth2\Client($id, $secret);

        if(!$request->query->has('code')){
            $url = $client->getAuthenticationUrl($base.'/oauth/v2/auth', $this->generateUrl('user_login', [],UrlGeneratorInterface::ABSOLUTE_URL));
            return $this->redirect($url);
        }else{
            $params = ['code' => $request->query->get('code'), 'redirect_uri' => $this->generateUrl('user_login', [],UrlGeneratorInterface::ABSOLUTE_URL)];
            $resp = $client->getAccessToken($base.'/oauth/v2/token', 'authorization_code', $params);

            if(isset($resp['result']) && isset($resp['result']['access_token'])){
                $info = $resp['result'];

                $client->setAccessTokenType(\OAuth2\Client::ACCESS_TOKEN_BEARER);
                $client->setAccessToken($info['access_token']);
                $response = $client->fetch($base.'/api/user/me');
                $data = $response['result'];

                $username = $data['username'];

                $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['userName'=>$username]);
                if($user === null){ // Création de l'utilisateur s'il n'existe pas
                    $user = new User;
                    $user->setUsername($username)
                        ->setPassword(sha1(uniqid()))
                        ->setEmail($data['email'])
                        ->setLastName($data['nom'])
                        ->setFirstName($data['prenom'])
                        ->setRoles(['ROLE_USER']);

                    $entityManager->persist($user);
                    $entityManager->flush();
                }

                // Connexion effective de l'utilisateur
                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $tokenStorage->setToken($token);

                $session->set('_security_main', serialize($token));

                $event = new InteractiveLoginEvent($request, $token);
                $dispatcher->dispatch("security.interactive_login", $event);
            }

            // Redirection vers l'accueil
            return $this->redirectToRoute('home');
        }
    }
}