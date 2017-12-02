<?php
declare(strict_types=1);

namespace App\Test\Security;

use App\Security\ApiUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\User;

/**
 * @covers \App\Security\ApiUser
 */
class ApiUserTest extends TestCase
{
    public function testGeneric()
    {
        $user = new ApiUser(['username' => 'henk', 'token' => 'foobar']);

        self::assertSame('henk', $user->getUsername());
        self::assertSame('', $user->getPassword());
        self::assertSame('', $user->getSalt());
        self::assertSame(['ROLE_USER', 'ROLE_API_USER'], $user->getRoles());

        $user->eraseCredentials();

        self::assertTrue($user->isEqualTo(new ApiUser(['username' => 'henk', 'token' => 'foobar'])));
        self::assertFalse($user->isEqualTo(new ApiUser(['username' => 'hans', 'token' => 'foobar'])));
        self::assertFalse($user->isEqualTo(new User('henk', 'foobar')));
    }
}
