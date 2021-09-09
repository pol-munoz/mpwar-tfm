<?php

namespace Kunlabo\User\Infrastructure\Framework\Controller;

use Kunlabo\Participant\Application\Query\SearchNewParticipantsByStudyId\SearchNewParticipantsByStudyIdQuery;
use Kunlabo\Participant\Domain\Participant;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Application\Query\SearchStudiesByOwnerId\SearchStudiesByOwnerIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'web_home', methods: ['GET'])]
    public function home(
        QueryBus $queryBus,
        Security $security
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $owner = $security->getUser()->getId();
        $studies = $queryBus->ask(SearchStudiesByOwnerIdQuery::fromOwnerId($owner))->getStudies();

        $studyLookup = [];
        $participants = [];
        foreach ($studies as $study) {
            $result = $queryBus->ask(SearchNewParticipantsByStudyIdQuery::fromStudyId($study->getId()))->getParticipants();
            $participants = array_merge($participants, $result);
            $studyLookup[$study->getId()->getRaw()] = $study;
        }

        usort($participants, function (Participant $a, Participant $b) { return $b->getCreated() > $a->getCreated() ? 1 : - 1; });

        return $this->render("app/home.html.twig", [
            'studies' => $studyLookup,
            'participants' => $participants
        ]);
    }
}