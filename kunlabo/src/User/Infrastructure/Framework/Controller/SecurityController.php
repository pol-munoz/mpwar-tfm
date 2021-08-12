<?php

namespace Kunlabo\User\Infrastructure\Framework\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'web_login', methods: ['GET', 'POST'])]
    public function login(
        AuthenticationUtils $authUtils
    ): Response {
        return $this->render(
            'auth/login.html.twig',
            [
                'last_username' => $authUtils->getLastUsername(),
                'error' => $authUtils->getLastAuthenticationError(),
            ]
        );
    }

    #[Route('/logout', name: 'web_logout')]
    public function logout(): void
    {
        throw new AuthenticationException('Log me out');
    }
}
