<?php

namespace App\Service;

use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use function Symfony\Component\String\u;

class LoginAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $data = $request->request->all();
        
        $input = [
            'email' => empty($data['email']) ? null : (string) u($data['email'])->trim()->lower(),
            'password' => empty($data['password']) ? null : (string) u($data['password'])->trim(),
        ];

        if ($input['email'] == null || $input['password'] == null) {
            throw new CustomUserMessageAuthenticationException('Email or password was not provided');
        }

        return new Passport(new UserBadge($input['email']), new PasswordCredentials($input['password']));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        if (array_key_exists('code', $exception->getMessageData())) {
            $data['code'] = $exception->getMessageData()['code'];
        }

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}