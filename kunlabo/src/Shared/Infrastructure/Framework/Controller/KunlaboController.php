<?php

namespace Kunlabo\Shared\Infrastructure\Framework\Controller;

use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class KunlaboController extends AbstractController
{
    #[Route('/', name: 'web_landing', methods: ['GET'])]
    public function landing(): Response
    {
        return $this->render("landing.html.twig");
    }
}