<?php

namespace Kunlabo\Study\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Agent\Application\Query\SearchAgentsByOwnerId\SearchAgentsByOwnerIdQuery;
use Kunlabo\Agent\Domain\Exception\UnknownAgentException;
use Kunlabo\Engine\Application\Query\FindEngineById\FindEngineByIdQuery;
use Kunlabo\Engine\Application\Query\SearchEnginesByOwnerId\SearchEnginesByOwnerIdQuery;
use Kunlabo\Engine\Domain\Exception\UnknownEngineException;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\Study\Application\Command\CreateStudy\CreateStudyCommand;
use Kunlabo\Study\Application\Query\SearchStudiesByOwnerId\SearchStudiesByOwnerIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;

final class NewStudyController extends AbstractController
{
    #[Route('/new', name: 'web_studies_new', methods: ['GET'])]
    public function newStudy(
        QueryBus $queryBus,
        Security $security
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $owner = $security->getUser()->getId();

        $studies = $queryBus->ask(SearchStudiesByOwnerIdQuery::fromOwnerId($owner))->getStudies();
        $engines = $queryBus->ask(SearchEnginesByOwnerIdQuery::fromOwnerId($owner))->getEngines();
        $agents = $queryBus->ask(SearchAgentsByOwnerIdQuery::fromOwnerId($owner))->getAgents();

        return $this->render(
            'app/studies/new.html.twig',
            ['studies' => $studies, 'engines' => $engines, 'agents' => $agents]
        );
    }

    #[Route('/new', name: 'web_studies_new_post', methods: ['POST'])]
    public function newStudyPost(
        Request $request,
        QueryBus $queryBus,
        CommandBus $commandBus,
        Security $security,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $uuid = Uuid::random();
        $name = $request->request->get('name', '');
        $owner = $security->getUser()->getId();
        $engineId = $request->request->get('engine', '');
        $agentId = $request->request->get('agent', '');

        try {
            // MARK this kind of breaks the aggregate boundary
            $engineQuery = FindEngineByIdQuery::fromString($engineId);
            $engine = $queryBus->ask($engineQuery)->getEngine();
            if ($engine === null) {
                throw new UnknownEngineException($engineQuery->getId());
            }

            // MARK this kind of breaks the aggregate boundary
            $agentQuery = FindAgentByIdQuery::fromString($agentId);
            $agent = $queryBus->ask($agentQuery)->getAgent();
            if ($agent === null) {
                throw new UnknownAgentException($agentQuery->getId());
            }

            $commandBus->dispatch(CreateStudyCommand::create($uuid, $name, $owner, $engineId, $agentId));

            return new RedirectResponse($urlGenerator->generate('web_studies'), Response::HTTP_SEE_OTHER);
        } catch (DomainException $exception) {
            $studies = $queryBus->ask(SearchStudiesByOwnerIdQuery::fromOwnerId($owner))->getStudies();
            $engines = $queryBus->ask(SearchEnginesByOwnerIdQuery::fromOwnerId($owner))->getEngines();
            $agents = $queryBus->ask(SearchAgentsByOwnerIdQuery::fromOwnerId($owner))->getAgents();

            return new Response(
                $this->renderView(
                    'app/studies/new.html.twig',
                    [
                        'error' => $exception->getMessage(),
                        'studies' => $studies,
                        'engines' => $engines,
                        'agents' => $agents
                    ]
                ), Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}