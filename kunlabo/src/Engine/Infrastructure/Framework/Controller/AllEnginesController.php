<?php

namespace Kunlabo\Engine\Infrastructure\Framework\Controller;

use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AllEnginesController extends AbstractController
{
    #[Route('/engines', name: 'web_engines', methods: ['GET'])]
    public function allEngines(): Response
    {
        return $this->render('app/engines.html.twig');
    }

    #[Route('/engines', name: 'web_engines_post', methods: ['POST'])]
    public function allEnginesPost(Request $request): Response
    {
        try {
            // TODO

            return new RedirectResponse($request->getUri(), Response::HTTP_SEE_OTHER);
        } catch (DomainException $exception) {
            return new Response(
                $this->renderView('app/engines.html.twig', ['error' => $exception->getMessage()]),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}