<?php
declare(strict_types=1);

namespace App\Tests\Exception;

use App\Exception\ConfigException;
use App\Exception\PublishException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Exception\PublishException
 */
class PublishExceptionTest extends TestCase
{
    public function testDataNotChanged()
    {
        $e = PublishException::dataNotChanged('foobar');

        self::assertSame('Data has not changed for "foobar".', $e->getMessage());
        self::assertSame(2001, $e->getCode());
    }

    public function testDataNotValid()
    {
        $e = PublishException::dataNotValid(['foo', 'bar']);

        self::assertSame('Fields "foo", "bar" are not supported.', $e->getMessage());
        self::assertSame(2002, $e->getCode());
    }

    public function testRevisionMismatch()
    {
        $e = PublishException::revisionMismatch('foobar', 'a', 'b');

        self::assertSame('Revision mismatch for "foobar", got "b" but expected "a".', $e->getMessage());
        self::assertSame(2003, $e->getCode());
    }
}
