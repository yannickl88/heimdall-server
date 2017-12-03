<?php
declare(strict_types=1);

namespace App\Tests\Exception;

use App\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Exception\ConfigException
 */
class ConfigExceptionTest extends TestCase
{
    public function testIdentifierBadFormat()
    {
        $e = ConfigException::identifierBadFormat('foobar');

        self::assertSame('Identifier "foobar" has a bad format.', $e->getMessage());
        self::assertSame(1001, $e->getCode());
    }

    public function testIdentifierNotFound()
    {
        $e = ConfigException::identifierNotFound('foobar');

        self::assertSame('Unknown identifier "foobar".', $e->getMessage());
        self::assertSame(1002, $e->getCode());
    }

    public function testIdentifierAlreadyExists()
    {
        $e = ConfigException::identifierAlreadyExists('foobar');

        self::assertSame('Identifier "foobar" already exists.', $e->getMessage());
        self::assertSame(1003, $e->getCode());
    }
}
