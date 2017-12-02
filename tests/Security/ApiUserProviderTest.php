<?php
declare(strict_types=1);

namespace App\Test\Security;

use App\Security\ApiUser;
use App\Security\ApiUserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\User;

/**
 * @covers \App\Security\ApiUserProvider
 */
class ApiUserProviderTest extends TestCase
{
    /**
     * @var ApiUserProvider
     */
    private $api_user_provider;

    protected function setUp()
    {
        $this->api_user_provider = new ApiUserProvider(__DIR__);
    }

    public function testLoadUserByUsername()
    {
        $user = $this->api_user_provider->loadUserByUsername('foobar');

        self::assertSame('henk', $user->getUsername());
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameBadToken()
    {
        $this->api_user_provider->loadUserByUsername('../../');
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByNoToken()
    {
        $this->api_user_provider->loadUserByUsername('phpunit');
    }

    public function testRefreshUser()
    {
        $old_user = new ApiUser(['username' => 'hans', 'token' => 'foobar']);
        $user = $this->api_user_provider->refreshUser($old_user);

        self::assertSame('henk', $user->getUsername());
        self::assertNotSame($old_user, $user);
    }

    /**
     * @expectedException \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testRefreshUserWrontUserType()
    {
        $this->api_user_provider->refreshUser(new User('foo', 'bar'));
    }

    /**
     * @dataProvider supportClassProvider
     */
    public function testSupportsClass(bool $expected, string $class)
    {
        self::assertSame($expected, $this->api_user_provider->supportsClass($class));
    }

    public static function supportClassProvider()
    {
        return [
            [true, ApiUser::class],
            [false, User::class],
            [false, \stdClass::class],
            [false, \RuntimeException::class],
        ];
    }
}
