<?php

namespace Kunlabo\User\Infrastructure\Framework\Controller;

use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'web_home', methods: ['GET'])]
    public function home(): Response
    {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        return $this->render("app/home.html.twig");
    }
}