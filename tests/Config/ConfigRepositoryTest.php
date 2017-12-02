<?php
declare(strict_types=1);

namespace App\Tests\Config;

use App\Config\ConfigDataValidator;
use App\Config\ConfigPublisher;
use App\Config\ConfigRepository;
use App\Config\Data\Config;
use App\Exception\ConfigException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\ConfigRepository
 */
class ConfigRepositoryTest extends TestCase
{
    private $config_data_validator;

    /**
     * @var ConfigRepository
     */
    private $config_repository;

    protected function setUp()
    {
        $this->config_data_validator = $this->prophesize(ConfigDataValidator::class);

        $this->config_repository = new ConfigRepository(
            $this->config_data_validator->reveal(),
            __DIR__ . '/fixtures'
        );
    }

    public function testIdentifiers()
    {
        self::assertSame([
            'foobar'
        ], $this->config_repository->identifiers());
    }

    public function testGet()
    {
        $config = $this->config_repository->get('foobar');

        self::assertEquals([
            'identifier' => 'foobar',
            'parent_revision' => '2376fcd9f52a5b6bb3419b0896f4b07b',
            'revision' => '828c7728cbe8364d2b720d3454e2dfac',
            'data' => [
                'description' => 'foobar'
            ],
        ], $config->jsonSerialize());
    }

    /**
     * @expectedException \App\Exception\ConfigException
     * @expectedExceptionCode 1002
     */
    public function testGetUnknown()
    {
        $this->config_repository->get('barbaz');
    }
}
