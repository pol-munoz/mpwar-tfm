<?php

namespace Kunlabo\Engine\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Engine\Application\Command\CreateEngineFile\CreateEngineFileCommand;
use Kunlabo\Engine\Application\Query\FindEngineById\FindEngineByIdQuery;
use Kunlabo\Engine\Domain\Engine;
use Kunlabo\Engine\Domain\EngineFile;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class EngineController extends AbstractController
{
    #[Route('/{id}', name: 'web_engines_by_id', methods: ['GET'])]
    public function engine(
        QueryBus $queryBus,
        string $id
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $uuid = Uuid::fromRaw($id);
            $engine = $queryBus->ask(FindEngineByIdQuery::fromId($uuid))->getEngine();

            if ($engine === null) {
                throw $this->createNotFoundException();
            }
            return $this->render('app/engines/engine.html.twig', ['engine' => $engine]);
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
            $file->move($full, $name);

            $commandBus->dispatch(CreateEngineFileCommand::create($id, $path . $name));
        }

        return new Response($path, Response::HTTP_OK);
    }
}