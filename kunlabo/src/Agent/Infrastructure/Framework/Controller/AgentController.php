<?php

namespace Kunlabo\Agent\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Agent\Application\Command\CreateAgentFile\CreateAgentFileCommand;
use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Agent\Application\Query\SearchAgentFilesByAgentId\SearchAgentFilesByAgentIdQuery;
use Kunlabo\Agent\Domain\AgentFile;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\Utils;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AgentController extends AbstractController
{
    #[Route('/{id}', name: 'web_agents_by_id', methods: ['GET'])]
    public function agent(
        QueryBus $queryBus,
        string $id
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $uuid = Uuid::fromRaw($id);
            $agent = $queryBus->ask(FindAgentByIdQuery::fromId($uuid))->getAgent();

            if ($agent === null) {
                throw $this->createNotFoundException();
            }

            $files = $queryBus->ask(SearchAgentFilesByAgentIdQuery::fromAgentId($uuid))->getAgentFiles();

            $output = [];
            foreach ($files as $file) {
                $path = $file->getPath();
                Utils::expandPath($path, substr($path, 1), $output);
            }

            return $this->render('app/agents/agent.html.twig', ['agent' => $agent, 'files' => $output]);
        } catch (DomainException) {
            throw $this->createNotFoundException();
        }
    }

    #[Route('/{id}', name: 'web_agents_by_id_post', methods: ['POST'])]
    public function agentPost(
        Request $request,
        CommandBus $commandBus,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        // This is application-layer stuff
        $path = $request->request->get('path');
        $file = $request->files->get('file');

        if ($file) {
            $full = AgentFile::BASE_PATH . $id . $path;
            $name = $file->getClientOriginalName();
            $file->move($full, $name);

            $commandBus->dispatch(CreateAgentFileCommand::create($id, $path . $name));
        }

        return new Response();
    }
}