<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response //AuthenticationUtils reading from session by Security::AUTHENTICATION_ERROR key
    {
        return $this->render('security/login.html.twig',[
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUsername(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     * @throws \Exception
     */
    public function logout(): Response
    {
        throw new \Exception('logout() should never be reached');
    }

    /**
     * @Route("/revoke", name="app_revoke")
     */
    public function revokeToken(HttpClientInterface $httpClient): Response
    {
        $response = $httpClient->request('GET', 'http://127.0.0.1:8000/api/user/45/revoke');
        dd($response);
    }
}
