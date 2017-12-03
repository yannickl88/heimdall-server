<?php
declare(strict_types=1);

namespace App\Tests\Functional;


use App\Security\ApiUser;
use App\Security\ApiUserProvider;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class TestApiUserProvider extends ApiUserProvider
{
    public function __construct()
    {
    }

    public function loadUserByUsername($token): ApiUser
    {
        if (1 !== preg_match('/^[0-9a-z]+$/', $token)) {
            throw new UsernameNotFoundException(sprintf('Token "%s" invalid.', $token));
        }

        if ($token === 'testtoken') {
            return new ApiUser(['username' => 'phpunit', 'token' => $token]);
        }
        throw new UsernameNotFoundException(sprintf('Token "%s" not found.', $token));
    }
}
