<?php

namespace Kunlabo\Agent\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Agent\Application\Command\CreateAgent\CreateAgentCommand;
use Kunlabo\Agent\Application\Command\DeleteAgent\DeleteAgentCommand;
use Kunlabo\Agent\Application\Query\SearchAgentsByOwnerId\SearchAgentsByOwnerIdQuery;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

final class AllAgentsController extends AbstractController
{
    #[Route('/', name: 'web_agents', methods: ['GET'])]
    public function allAgents(
        QueryBus $queryBus,
        Security $security
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $owner = $security->getUser()->getId();

        $agents = $queryBus->ask(SearchAgentsByOwnerIdQuery::fromOwnerId($owner))->getAgents();

        return $this->render('app/agents/agents.html.twig', ['agents' => $agents]);
    }

    #[Route('/', name: 'web_agents_post', methods: ['POST'])]
    public function allAgentsPost(
        Request $request,
        QueryBus $queryBus,
        CommandBus $commandBus,
        Security $security
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $uuid = Uuid::random();
        $name = $request->request->get('name', '');
        $owner = $security->getUser()->getId();
        $kind = $request->request->get('kind', '');

        try {
            $commandBus->dispatch(CreateAgentCommand::create($uuid, $name, $owner, $kind));

            return new RedirectResponse($request->getUri(), Response::HTTP_SEE_OTHER);
        } catch (DomainException $exception) {
            $agents = $queryBus->ask(SearchAgentsByOwnerIdQuery::fromOwnerId($owner))->getAgents();

            return new Response(
                $this->renderView(
                    'app/agents/agents.html.twig',
                    ['error' => $exception->getMessage(), 'agents' => $agents]
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    #[Route('/delete/{id}', name: 'web_agents_delete', methods: ['GET'])]
    public function engineDelete(
        CommandBus $commandBus,
        UrlGeneratorInterface $urlGenerator,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $commandBus->dispatch(DeleteAgentCommand::create($id));

        return new RedirectResponse($urlGenerator->generate('web_agents'), Response::HTTP_SEE_OTHER);
    }
}