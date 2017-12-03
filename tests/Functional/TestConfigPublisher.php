<?php
declare(strict_types=1);

namespace App\Tests\Functional;


use App\Config\ConfigPublisher;
use App\Config\Data\Config;
use App\Exception\ConfigException;

class TestConfigPublisher extends ConfigPublisher
{
    public function __construct()
    {
    }

    public function init(string $identifier): Config
    {
        if ($identifier === 'foo') {
            throw ConfigException::identifierAlreadyExists($identifier);
        }

        return Config::init($identifier);
    }

    public function publish(string $identifier, string $parent_revision, array $data): Config
    {
        if ($identifier === 'foo') {
            return Config::fromArray([
                'identifier' => 'foo',
                'parent_revision' => 'phpunit',
                'data' => $data,
            ]);
        }

        throw ConfigException::identifierNotFound($identifier);
    }
}
