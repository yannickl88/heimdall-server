<?php
declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ApiUserProvider implements UserProviderInterface
{
    private $tokens_dir;

    public function __construct(string $tokens_dir)
    {
        $this->tokens_dir = $tokens_dir;
    }

    public function loadUserByUsername($token): ApiUser
    {
        if (1 !== preg_match('/^[0-9a-z]+$/', $token)) {
            throw new UsernameNotFoundException(sprintf('Token "%s" invalid.', $token));
        }

        $file = $this->tokens_dir . '/' . $token . '.json';

        if (!file_exists($file)) {
            throw new UsernameNotFoundException(sprintf('Token "%s" not found.', $token));
        }

        return new ApiUser(array_merge(json_decode(file_get_contents($file), true), ['token' => $token]));
    }

    public function refreshUser(UserInterface $user): ApiUser
    {
        if (!$user instanceof ApiUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getTokenKey());
    }

    public function supportsClass($class): bool
    {
        return ApiUser::class === $class;
    }
}
