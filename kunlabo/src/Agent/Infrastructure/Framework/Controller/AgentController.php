<?php

namespace Kunlabo\Agent\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Agent\Application\Command\CreateAgentFile\CreateAgentFileCommand;
use Kunlabo\Agent\Application\Command\DeleteAgent\DeleteAgentCommand;
use Kunlabo\Agent\Application\Command\DeleteAgentFile\DeleteAgentFileCommand;
use Kunlabo\Agent\Application\Command\SetAgentMainFile\SetAgentMainFileCommand;
use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Agent\Application\Query\SearchAgentFilesByAgentId\SearchAgentFilesByAgentIdQuery;
use Kunlabo\Agent\Domain\AgentFile;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\Utils;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AgentController extends AbstractController
{
    #[Route('/{id}', name: 'web_agents_by_id', methods: ['GET'])]
    public function agent(
        QueryBus $queryBus,
        string $id
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $agent = $queryBus->ask(FindAgentByIdQuery::create($id))->getAgent();

            if ($agent === null) {
                throw $this->createNotFoundException();
            }

            $files = $queryBus->ask(SearchAgentFilesByAgentIdQuery::create($id))->getAgentFiles();

            $output = [];
            $items = [];

            foreach ($files as $file) {
                $path = $file->getPath();
                $items[$path] = $file;
                Utils::expandPath($path, substr($path, 1), $output);
            }

            return $this->render(
                'app/agents/agent.html.twig',
                ['agent' => $agent, 'paths' => $output, 'files' => $items]
            );
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

            $commandBus->dispatch(CreateAgentFileCommand::create($id, $path . $name));

            $file->move($full, $name);
        }

        return new Response();
    }

    #[Route('/file/main/{id}', name: 'web_agents_main_file_post', methods: ['POST'])]
    public function agentMainPost(
        Request $request,
        CommandBus $commandBus,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $main = $request->getContent();
        $commandBus->dispatch(SetAgentMainFileCommand::create($id, $main));

        return new Response();
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


    #[Route('/file/delete/{id}', name: 'web_agents_delete_file_post', methods: ['POST'])]
    public function engineDeletePost(
        Request $request,
        CommandBus $commandBus,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $path = $request->getContent();
        $commandBus->dispatch(DeleteAgentFileCommand::create($id, $path));

        return new Response();
    }
}