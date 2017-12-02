<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    public function start(Request $request, AuthenticationException $auth_exception = null)
    {
        return new JsonResponse([
            'error_code' => 1000,
            'error_message' => 'Invalid or missing token.'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function getCredentials(Request $request)
    {
        return [
            'token' => $request->query->get('token'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $user_provider)
    {
        if (null === ($token = $credentials['token'])) {
            return null;
        }

        return $user_provider->loadUserByUsername($token);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $provider_key)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'error_code' => 1000,
            'error_message' => 'Token is invalid or has expired.'
        ], Response::HTTP_FORBIDDEN);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
