<?php
declare(strict_types=1);

namespace App\Tests\Security;

use App\Security\ApiUser;
use App\Security\TokenAuthenticator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @covers \App\Security\TokenAuthenticator
 */
class TokenAuthenticatorTest extends TestCase
{
    /**
     * @var TokenAuthenticator
     */
    private $token_authenticator;

    protected function setUp()
    {
        $this->token_authenticator = new TokenAuthenticator();
    }

    public function testStart()
    {
        self::assertSame(
            '{"error_code":1000,"error_message":"Invalid or missing token."}',
            $this->token_authenticator->start(Request::create('/'))->getContent()
        );
    }

    public function testGetCredentials()
    {
        self::assertSame(
            ['token' => 'foobar'],
            $this->token_authenticator->getCredentials(Request::create('/', 'GET', ['token' => 'foobar']))
        );
    }

    public function testGetUser()
    {
        $user = new ApiUser(['username' => 'henk', 'token' => 'foobar']);

        $provider = $this->prophesize(UserProviderInterface::class);
        $provider->loadUserByUsername('foobar')->willReturn($user);

        self::assertSame($user, $this->token_authenticator->getUser(['token' => 'foobar'], $provider->reveal()));
    }

    public function testGetUserNoToken()
    {
        $provider = $this->prophesize(UserProviderInterface::class);
        $provider->loadUserByUsername('foobar')->shouldNotBeCalled();

        self::assertNull($this->token_authenticator->getUser(['token' => null], $provider->reveal()));
    }

    public function testCheckCredentials()
    {
        self::assertTrue($this->token_authenticator->checkCredentials(
            'foo',
            new ApiUser(['username' => 'henk', 'token' => 'foobar'])
        ));
    }

    public function testOnAuthenticationSuccess()
    {
        self::assertNull($this->token_authenticator->onAuthenticationSuccess(
            Request::create('/'),
            new UsernamePasswordToken('foo', 'bar', 'phpunit'),
            'phpunit'
        ));
    }

    public function testOnAuthenticationFailure()
    {
        self::assertSame(
            '{"error_code":1000,"error_message":"Token is invalid or has expired."}',
                $this->token_authenticator->onAuthenticationFailure(
                Request::create('/'),
                new AuthenticationException()
            )->getContent()
        );
    }

    public function testSupportsRememberMe()
    {
        self::assertFalse($this->token_authenticator->supportsRememberMe());
    }
}
