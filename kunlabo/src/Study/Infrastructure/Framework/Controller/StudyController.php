<?php

namespace Kunlabo\Study\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StudyController extends AbstractController
{
    #[Route('/{id}', name: 'web_studies_by_id', methods: ['GET'])]
    public function engine(
        QueryBus $queryBus,
        string $id
    ): Response {
        try {
            $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

            $uuid = Uuid::fromRaw($id);
            $study = $queryBus->ask(FindStudyByIdQuery::fromId($uuid))->getStudy();

            if ($study === null) {
                throw $this->createNotFoundException();
            }

            return $this->render('app/studies/study.html.twig', ['study' => $study]);
        } catch (DomainException) {
            throw $this->createNotFoundException();
        }
    }
}