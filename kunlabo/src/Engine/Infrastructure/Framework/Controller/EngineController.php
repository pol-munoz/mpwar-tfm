<?php

namespace Kunlabo\Engine\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Engine\Application\Command\CreateEngineFile\CreateEngineFileCommand;
use Kunlabo\Engine\Application\Command\DeleteEngine\DeleteEngineCommand;
use Kunlabo\Engine\Application\Command\DeleteEngineFile\DeleteEngineFileCommand;
use Kunlabo\Engine\Application\Command\SetEngineMainFile\SetEngineMainFileCommand;
use Kunlabo\Engine\Application\Query\FindEngineById\FindEngineByIdQuery;
use Kunlabo\Engine\Application\Query\SearchEngineFilesByEngineId\SearchEngineFilesByEngineIdQuery;
use Kunlabo\Engine\Domain\EngineFile;
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

final class EngineController extends AbstractController
{
    #[Route('/{id}', name: 'web_engines_by_id', methods: ['GET'])]
    public function engine(
        QueryBus $queryBus,
        string $id
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $engine = $queryBus->ask(FindEngineByIdQuery::create($id))->getEngine();

            if ($engine === null) {
                throw $this->createNotFoundException();
            }

            $files = $queryBus->ask(SearchEngineFilesByEngineIdQuery::create($id))->getEngineFiles();

            $output = [];
            $items = [];

            foreach ($files as $file) {
                $path = $file->getPath();
                $items[$path] = $file;
                Utils::expandPath($path, substr($path, 1), $output);
            }

            return $this->render(
                'app/engines/engine.html.twig',
                ['engine' => $engine, 'paths' => $output, 'files' => $items]
            );
        } catch (DomainException) {
            throw $this->createNotFoundException();
        }
    }

    #[Route('/{id}', name: 'web_engines_by_id_post', methods: ['POST'])]
    public function enginePost(
        Request $request,
        CommandBus $commandBus,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        // This is application-layer stuff
        $path = $request->request->get('path');
        $file = $request->files->get('file');

        if ($file) {
            $full = EngineFile::BASE_PATH . $id . $path;
            $name = $file->getClientOriginalName();

            $commandBus->dispatch(CreateEngineFileCommand::create($id, $path . $name));

            $file->move($full, $name);
        }

        return new Response();
    }

    #[Route('/{id}/file/main', name: 'web_engines_main_file_post', methods: ['POST'])]
    public function engineMainPost(
        Request $request,
        CommandBus $commandBus,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $main = $request->getContent();
        $commandBus->dispatch(SetEngineMainFileCommand::create($id, $main));

        return new Response();
    }

    #[Route('/{id}/delete', name: 'web_engines_delete', methods: ['GET'])]
    public function engineDelete(
        CommandBus $commandBus,
        UrlGeneratorInterface $urlGenerator,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $commandBus->dispatch(DeleteEngineCommand::create($id));

        return new RedirectResponse($urlGenerator->generate('web_engines'), Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/file/delete', name: 'web_engines_delete_file_post', methods: ['POST'])]
    public function engineDeleteFilePost(
        Request $request,
        CommandBus $commandBus,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $path = $request->getContent();
        $commandBus->dispatch(DeleteEngineFileCommand::create($id, $path));

        return new Response();
    }
}