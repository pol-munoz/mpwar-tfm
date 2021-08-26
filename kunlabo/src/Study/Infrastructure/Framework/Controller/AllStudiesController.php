<?php

namespace Kunlabo\Study\Infrastructure\Framework\Controller;

use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Study\Application\Command\DeleteStudy\DeleteStudyCommand;
use Kunlabo\Study\Application\Query\SearchStudiesByOwnerId\SearchStudiesByOwnerIdQuery;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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


    #[Route('/{id}/delete', name: 'web_studies_delete', methods: ['GET'])]
    public function engineDelete(
        CommandBus $commandBus,
        UrlGeneratorInterface $urlGenerator,
        string $id
    ): Response {
        $this->denyAccessUnlessGranted(AuthUser::ROLE_RESEARCHER);

        $commandBus->dispatch(DeleteStudyCommand::create($id));

        return new RedirectResponse($urlGenerator->generate('web_studies'), Response::HTTP_SEE_OTHER);
    }
}