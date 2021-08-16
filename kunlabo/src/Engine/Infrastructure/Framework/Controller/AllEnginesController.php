<?php

namespace Kunlabo\Engine\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Engine\Application\Command\CreateEngine\CreateEngineCommand;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class AllEnginesController extends AbstractController
{
    #[Route('/engines', name: 'web_engines', methods: ['GET'])]
    public function allEngines(): Response
    {
        return $this->render('app/engines.html.twig');
    }

    #[Route('/engines', name: 'web_engines_post', methods: ['POST'])]
    public function allEnginesPost(Request $request, CommandBus $commandBus, Security $security): Response
    {
        $owner = $security->getUser()->getId();
        $name = $request->request->get('name', '');
        $uuid = Uuid::random();

        try {
            $commandBus->dispatch(CreateEngineCommand::create($uuid, $name, $owner));

            return new RedirectResponse($request->getUri(), Response::HTTP_SEE_OTHER);
        } catch (DomainException $exception) {
            return new Response(
                $this->renderView('app/engines.html.twig', ['error' => $exception->getMessage()]),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}