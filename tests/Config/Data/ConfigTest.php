<?php
declare(strict_types=1);

namespace App\Tests\Config\Data;

use App\Config\Data\Config;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\Data\Config
 */
class ConfigTest extends TestCase
{
    public function testInit()
    {
        $config = Config::init('foobar');

        self::assertSame('2376fcd9f52a5b6bb3419b0896f4b07b', $config->getRevision());
        self::assertSame([
            'identifier' => 'foobar',
            'parent_revision' => '',
            'revision' => '2376fcd9f52a5b6bb3419b0896f4b07b',
            'data' => [],
        ], $config->jsonSerialize());
    }

    public function testNewRevision()
    {
        $previous = Config::init('foobar');

        $config = Config::newRevision($previous, ['foo' => 'bar']);

        self::assertSame('90a9ebb9d1cb1fb76a16747f817c04f0', $config->getRevision());
        self::assertSame([
            'identifier' => 'foobar',
            'parent_revision' => '2376fcd9f52a5b6bb3419b0896f4b07b',
            'revision' => '90a9ebb9d1cb1fb76a16747f817c04f0',
            'data' => ['foo' => 'bar'],
        ], $config->jsonSerialize());
    }

    /**
     * @expectedException \App\Exception\NoChangeException
     */
    public function testNewRevisionSameData()
    {
        $previous = Config::fromArray([
            'identifier' => 'foobar',
            'parent_revision' => 'phpunit',
            'data' => ['foo' => 'bar'],
        ]);
        Config::newRevision($previous, ['foo' => 'bar']);
    }

    public function testFromArray()
    {
        $config = Config::fromArray([
            'identifier' => 'foobar',
            'parent_revision' => 'phpunit',
            'data' => ['foo' => 'bar'],
        ]);

        self::assertSame('b63ddbd1dba637fd9efb7b26b5d0ee76', $config->getRevision());
        self::assertSame([
            'identifier' => 'foobar',
            'parent_revision' => 'phpunit',
            'revision' => 'b63ddbd1dba637fd9efb7b26b5d0ee76',
            'data' => ['foo' => 'bar'],
        ], $config->jsonSerialize());
    }
}
