<?php

namespace Kunlabo\Engine\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Engine\Application\Query\FindEngineById\FindEngineByIdQuery;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class EngineController extends AbstractController
{
    #[Route('/{id}', name: 'web_engines_by_id', methods: ['GET'])]
    public function engine(
        QueryBus $queryBus,
        string $id
    ): Response {
        try {
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
}