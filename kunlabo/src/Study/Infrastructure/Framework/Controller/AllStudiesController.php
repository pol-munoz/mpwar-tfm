<?php

namespace Kunlabo\Study\Infrastructure\Framework\Controller;

use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Application\Query\SearchStudiesByOwnerId\SearchStudiesByOwnerIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

final class AllStudiesController extends AbstractController
{
    #[Route('/', name: 'web_studies', methods: ['GET'])]
    public function allEngines(
        QueryBus $queryBus,
        Security $security
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $owner = $security->getUser()->getId();

        $studies = $queryBus->ask(SearchStudiesByOwnerIdQuery::fromOwnerId($owner))->getStudies();

        return $this->render('app/studies/studies.html.twig', ['studies' => $studies]);
    }
}