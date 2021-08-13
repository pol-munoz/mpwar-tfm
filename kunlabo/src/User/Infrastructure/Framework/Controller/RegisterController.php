<?php

namespace Kunlabo\User\Infrastructure\Framework\Controller;

use DomainException;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\Shared\Domain\ValueObject\Uuid;
use Kunlabo\User\Application\Command\SignUp\SignUpCommand;
use Kunlabo\User\Application\Query\FindByIdQuery\FindByIdQuery;
use Kunlabo\User\Domain\Exception\UserAlreadyExistsException;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Kunlabo\User\Infrastructure\Framework\Auth\Guard\LoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'web_register', methods: ['GET'])]
    public function register(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    #[Route('/register', name: 'web_register_post', methods: ['POST'])]
    public function registerPost(
        Request $request,
        QueryBus $queryBus,
        CommandBus $commandBus,
        UserAuthenticatorInterface $authenticator,
        LoginAuthenticator $loginAuthenticator
    ): Response {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $uuid = Uuid::random();

        try {
            $commandBus->dispatch(SignUpCommand::fromRaw($uuid, $email, $password));

            // A bit weird but needed to automatically authenticate the user
            $user = $queryBus->ask(FindByIdQuery::fromId($uuid))->getUser();
            $user = AuthUser::fromDomainUser($user);

            return $authenticator->authenticateUser(
                $user,
                $loginAuthenticator,
                $request
            );
        } catch (UserAlreadyExistsException $exception) {
            return new Response(
                $this->renderView('auth/register.html.twig', ['error' => $exception->getMessage(), 'email' => $email]),
                Response::HTTP_CONFLICT
            );
        } catch (DomainException $exception) {
            return new Response(
                $this->renderView('auth/register.html.twig', ['error' => $exception->getMessage(), 'email' => $email]),
                Response::HTTP_BAD_REQUEST
            );
        }
    }

}