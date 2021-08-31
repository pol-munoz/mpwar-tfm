<?php


namespace Kunlabo\Engine\Infrastructure\Framework\Controller;


use DomainException;
use Kunlabo\Engine\Application\Command\CreateEngineFile\CreateEngineFileCommand;
use Kunlabo\Engine\Application\Query\FindEngineById\FindEngineByIdQuery;
use Kunlabo\Engine\Application\Query\SearchEngineFilesByEngineIdAndFolder\SearchEngineFilesByEngineIdAndFolderQuery;
use Kunlabo\Engine\Domain\EngineFile;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\Utils;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EngineFolderController extends AbstractController
{
    #[Route('/{id}{folder}', name: 'web_engines_by_id_and_folder', requirements: ['folder' => '.+'], methods: ['GET'])]
    public function engineFolder(
        QueryBus $queryBus,
        string $id,
        string $folder,
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $engine = $queryBus->ask(FindEngineByIdQuery::create($id))->getEngine();

            if ($engine === null) {
                throw $this->createNotFoundException();
            }

            $files = $queryBus->ask(SearchEngineFilesByEngineIdAndFolderQuery::create($id, $folder))->getEngineFiles();

            $output = [];
            $items = [];

            foreach ($files as $file) {
                $path = $file->getPath();
                $items[$path] = $file;
                Utils::expandPath($path, substr($path, 1), $output);
            }

            return $this->render(
                'app/engines/folder.html.twig',
                ['engine' => $engine, 'paths' => $output, 'files' => $items, 'folder' => $folder . '/']
            );
        } catch (DomainException) {
            throw $this->createNotFoundException();
        }
    }

    #[Route('/{id}{folder}', name: 'web_engines_by_id_and_folder_post', requirements: ['folder' => '.+'], methods: ['POST'])]
    public function engineFolderPost(
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
            $full = EngineFile::BASE_PATH . $id . $path;
            $name = $file->getClientOriginalName();

            $commandBus->dispatch(CreateEngineFileCommand::create($id, $path . $name));

            $file->move($full, $name);
        }

        return new Response();
    }
}