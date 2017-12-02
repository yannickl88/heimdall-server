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
 * @covers \App\Config\ConfigPublisher
 */
class ConfigPublisherTest extends TestCase
{
    private $config_repository;
    private $config_data_validator;

    /**
     * @var ConfigPublisher
     */
    private $config_publisher;

    protected function setUp()
    {
        $this->config_repository = $this->prophesize(ConfigRepository::class);
        $this->config_data_validator = $this->prophesize(ConfigDataValidator::class);

        $this->config_publisher = new ConfigPublisher(
            $this->config_repository->reveal(),
            $this->config_data_validator->reveal(),
            __DIR__,
            __DIR__
        );
    }

    protected function tearDown()
    {
        @unlink(__DIR__ . '/foobar.json');
        @unlink(__DIR__ . '/foobar.2376fcd9f52a5b6bb3419b0896f4b07b.json');
        @unlink(__DIR__ . '/foobar.828c7728cbe8364d2b720d3454e2dfac.json');
    }

    public function testInit()
    {
        $this->config_repository->get('foobar')->willThrow(new ConfigException('foobar'));

        $config = $this->config_publisher->init('foobar');

        self::assertEquals([
            'identifier' => 'foobar',
            'parent_revision' => '',
            'revision' => '2376fcd9f52a5b6bb3419b0896f4b07b',
            'data' => [],
        ], $config->jsonSerialize());
    }

    /**
     * @expectedException \App\Exception\ConfigException
     * @expectedExceptionCode 1003
     */
    public function testInitAlreadyExists()
    {
        $this->config_repository->get('foobar')->willReturn(Config::init('foobar'));

        $this->config_publisher->init('foobar');
    }

    public function testPublish()
    {
        $previous = Config::fromArray([
            'identifier' => 'foobar',
            'parent_revision' => '',
            'revision' => '2376fcd9f52a5b6bb3419b0896f4b07b',
            'data' => [],
        ]);

        $this->config_repository->get('foobar')->willReturn($previous);

        $config = $this->config_publisher->publish('foobar', '2376fcd9f52a5b6bb3419b0896f4b07b', [
            'description' => 'foobar'
        ]);

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
    public function testPublishUnknown()
    {
        $this->config_repository->get('foobar')->willThrow(new ConfigException('foobar'));

        $this->config_publisher->publish('foobar', '2376fcd9f52a5b6bb3419b0896f4b07b', [
            'description' => 'foobar'
        ]);
    }

    /**
     * @expectedException \App\Exception\PublishException
     * @expectedExceptionCode 2001
     */
    public function testPublishBadData()
    {
        $previous = Config::fromArray([
            'identifier' => 'foobar',
            'parent_revision' => '',
            'revision' => '2376fcd9f52a5b6bb3419b0896f4b07b',
            'data' => [],
        ]);

        $this->config_repository->get('foobar')->willReturn($previous);

        $this->config_publisher->publish('foobar', '2376fcd9f52a5b6bb3419b0896f4b07b', []);
    }

    /**
     * @expectedException \App\Exception\PublishException
     * @expectedExceptionCode 2003
     */
    public function testPublishBadParent()
    {
        $previous = Config::fromArray([
            'identifier' => 'foobar',
            'parent_revision' => '',
            'revision' => '2376fcd9f52a5b6bb3419b0896f4b07b',
            'data' => [],
        ]);

        $this->config_repository->get('foobar')->willReturn($previous);

        $this->config_publisher->publish('foobar', 'someotherrevision', [
            'description' => 'foobar'
        ]);
    }
}
