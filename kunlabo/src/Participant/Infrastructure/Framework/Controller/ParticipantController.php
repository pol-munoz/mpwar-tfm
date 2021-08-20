<?php

namespace Kunlabo\Participant\Infrastructure\Framework\Controller;

use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ParticipantController extends AbstractController
{
    public const STUDIES_SESSION_KEY = 'studies';

    #[Route('/{id}', name: 'web_participant', methods: ['GET'])]
    public function participant(
        QueryBus $queryBus,
        SessionInterface $session,
        UrlGeneratorInterface $urlGenerator,
        string $id
    ): Response {
        // MARK this kind of breaks the aggregate boundary
        $studyId = Uuid::fromRaw($id);
        $study = $queryBus->ask(FindStudyByIdQuery::fromId($studyId))->getStudy();

        if ($study === null) {
            throw $this->createNotFoundException();
        }

        if (!$session->has(self::STUDIES_SESSION_KEY) || !array_key_exists(
                $id,
                $session->get(self::STUDIES_SESSION_KEY)
            )) {
            return new RedirectResponse(
                $urlGenerator->generate('web_participant_survey', ['id' => $id]), Response::HTTP_SEE_OTHER
            );
        }

        // TODO remove user id here
        return $this->render('study/study.html.twig', ['study' => $study, 'participant' => $session->get(self::STUDIES_SESSION_KEY)[$id]]);
    }
}