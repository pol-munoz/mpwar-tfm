<?php


namespace Kunlabo\Agent\Infrastructure\Framework\Controller;


use DomainException;
use Kunlabo\Agent\Application\Command\CreateAgentFile\CreateAgentFileCommand;
use Kunlabo\Agent\Application\Query\FindAgentById\FindAgentByIdQuery;
use Kunlabo\Agent\Application\Query\SearchAgentFilesByAgentIdAndFolder\SearchAgentFilesByAgentIdAndFolderQuery;
use Kunlabo\Agent\Domain\AgentFile;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\Utils;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class AgentFolderController extends AbstractController
{
    #[Route('/{id}{folder}', name: 'web_agents_by_id_and_folder', requirements: ['folder' => '.+'], methods: ['GET'])]
    public function agentFolder(
        QueryBus $queryBus,
        Security $security,
        string $id,
        string $folder,
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $agent = $queryBus->ask(FindAgentByIdQuery::create($id))->getAgent();

            $owner = $security->getUser()->getId();
            if ($agent === null || !$agent->isOwnedBy($owner)) {
                throw $this->createNotFoundException();
            }

            $files = $queryBus->ask(SearchAgentFilesByAgentIdAndFolderQuery::create($id, $folder))->getAgentFiles();

            $output = [];
            $items = [];

            foreach ($files as $file) {
                $path = $file->getPath();
                $items[$path] = $file;
                Utils::expandPath($path, substr($path, 1), $output);
            }

            return $this->render(
                'app/agents/folder.html.twig',
                ['agent' => $agent, 'paths' => $output, 'files' => $items, 'folder' => $folder . '/']
            );
        } catch (DomainException) {
            throw $this->createNotFoundException();
        }
    }

    #[Route('/{id}{folder}', name: 'web_agents_by_id_and_folder_post', requirements: ['folder' => '.+'], methods: ['POST'])]
    public function agentFolderPost(
        Request $request,
        CommandBus $commandBus,
        string $id,
        string $folder
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        // This is application-layer stuff
        $path = $folder . $request->request->get('path');
        $file = $request->files->get('file');

        if ($file) {
            $full = AgentFile::BASE_PATH . $id . $path;
            $name = $file->getClientOriginalName();

            $commandBus->dispatch(CreateAgentFileCommand::create($id, $path . $name));

            $file->move($full, $name);
        }

        return new Response();
    }
}