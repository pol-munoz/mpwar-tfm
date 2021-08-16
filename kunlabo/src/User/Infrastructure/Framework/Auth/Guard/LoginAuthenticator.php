<?php

namespace Kunlabo\User\Infrastructure\Framework\Auth\Guard;

use DomainException;
use Kunlabo\Shared\Application\Bus\Command\CommandBus;
use Kunlabo\Shared\Application\Bus\Query\QueryBus;
use Kunlabo\User\Application\Command\SignIn\SignInCommand;
use Kunlabo\User\Application\Query\SearchUserByEmail\SearchUserByEmailQuery;
use Kunlabo\User\Domain\Exception\InvalidCredentialsException;
use Kunlabo\User\Infrastructure\Framework\Auth\AuthUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class LoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private const LOGIN_ROUTE = 'web_login';

    private const SUCCESS_REDIRECT = 'web_home';

    public function __construct(
        private CommandBus $commandBus,
        private QueryBus $queryBus,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email', '');
        $plainPassword = $request->request->get('password', '');


        try {
            $this->commandBus->dispatch(SignInCommand::create($email, $plainPassword));

            $request->getSession()->set(Security::LAST_USERNAME, $email);

            return new Passport(
                new UserBadge(
                    $email, function (string $email): AuthUser {
                    $user = $this->queryBus->ask(SearchUserByEmailQuery::fromRaw($email))->getUser();
                    return AuthUser::fromDomainUser($user);
                }
                ),
                new PasswordCredentials($plainPassword),
                [
                    new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
                    new RememberMeBadge()
                ]
            );
        } catch (InvalidCredentialsException | DomainException $exception) {
            throw new AuthenticationException($exception->getMessage());
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath, Response::HTTP_SEE_OTHER);
        }

        return new RedirectResponse($this->urlGenerator->generate(self::SUCCESS_REDIRECT), Response::HTTP_SEE_OTHER);
    }
}