<?php

namespace Kunlabo\Engine\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Engine\Application\Command\CreateEngine\CreateEngineCommand;
use Kunlabo\Engine\Application\Query\SearchEnginesByOwnerId\SearchEnginesByOwnerIdQuery;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class AllEnginesController extends AbstractController
{
    #[Route('/', name: 'web_engines', methods: ['GET'])]
    public function allEngines(
        QueryBus $queryBus,
        Security $security
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $owner = $security->getUser()->getId();

        $engines = $queryBus->ask(SearchEnginesByOwnerIdQuery::fromOwnerId($owner))->getEngines();

        return $this->render('app/engines/engines.html.twig', ['engines' => $engines]);
    }

    #[Route('/', name: 'web_engines_post', methods: ['POST'])]
    public function allEnginesPost(
        Request $request,
        QueryBus $queryBus,
        CommandBus $commandBus,
        Security $security
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $owner = $security->getUser()->getId();
        $name = $request->request->get('name', '');
        $uuid = Uuid::random();

        try {
            $commandBus->dispatch(CreateEngineCommand::create($uuid, $name, $owner));

            return new RedirectResponse($request->getUri(), Response::HTTP_SEE_OTHER);
        } catch (DomainException $exception) {
            $engines = $queryBus->ask(SearchEnginesByOwnerIdQuery::fromOwnerId($owner))->getEngines();

            return new Response(
                $this->renderView(
                    'app/engines/engines.html.twig',
                    ['error' => $exception->getMessage(), 'engines' => $engines]
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}