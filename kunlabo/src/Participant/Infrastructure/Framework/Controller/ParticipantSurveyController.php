<?php

namespace Kunlabo\Participant\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Participant\Application\Command\SurveyFilled\SurveyFilledCommand;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\Study\Application\Query\FindStudyById\FindStudyByIdQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ParticipantSurveyController extends AbstractController
{
    #[Route('/{id}/survey', name: 'web_participant_survey', methods: ['GET'])]
    public function survey(
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

        if ($session->has(ParticipantController::STUDIES_SESSION_KEY) && array_key_exists(
                $id,
                $session->get(ParticipantController::STUDIES_SESSION_KEY)
            )) {
            return new RedirectResponse(
                $urlGenerator->generate('web_participant', ['id' => $id]), Response::HTTP_SEE_OTHER
            );
        }

        return $this->render('study/survey.html.twig', ['id' => $id]);
    }

    #[Route('/{id}/survey', name: 'web_participant_survey_post', methods: ['POST'])]
    public function surveyPost(
        Request $request,
        QueryBus $queryBus,
        CommandBus $commandBus,
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

        $uuid = Uuid::random();

        $nickname = $request->request->get('nickname', '');
        $age = $request->request->get('age', 0);
        $gender = $request->request->get('gender', '');
        $handedness = $request->request->get('handedness', '');

        try {
            $commandBus->dispatch(SurveyFilledCommand::create($uuid, $id, $nickname, $age, $gender, $handedness));

            $arr = [];
            if ($session->has(ParticipantController::STUDIES_SESSION_KEY)) {
                $arr = $session->get(ParticipantController::STUDIES_SESSION_KEY);
            }
            $arr[$id] = $uuid->getRaw();

            $session->set(ParticipantController::STUDIES_SESSION_KEY, $arr);

            return new RedirectResponse(
                $urlGenerator->generate('web_participant', ['id' => $id]), Response::HTTP_SEE_OTHER
            );
        } catch (DomainException $exception) {
            return new Response(
                $this->renderView(
                    'study/survey.html.twig',
                    [
                        'id' => $id,
                        'error' => $exception->getMessage(),
                        'nickname' => $nickname,
                        'age' => $age,
                        'gender' => $gender,
                        'handedness' => $handedness
                    ]
                ),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}