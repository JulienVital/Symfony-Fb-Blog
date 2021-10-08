<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\FacebookClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request): Response
    {
        /**
         * @var Session
         */
        $session = $request->getSession();
        return $this->render('security/login.html.twig',["error"=>null]);
    }

    /**
     * @Route("/connect", name="app_connect")
     */
    public function connect (ClientRegistry $clientRegistry) : RedirectResponse
    {
        /** @var FacebookClient $client */
        $client = $clientRegistry->getClient('facebook');

        return $client->redirect(['pages_show_list','pages_read_engagement']); 
    }

    /**
     * @Route("/facebook_check", name="facebook_check")
     */
    public function facebook_check (ClientRegistry $clientRegistry) 
    {
        
    }
    

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
