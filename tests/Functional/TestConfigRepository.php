<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Config\ConfigRepository;
use App\Config\Data\Config;
use App\Exception\ConfigException;

class TestConfigRepository extends ConfigRepository
{
    public function __construct()
    {
    }

    public function identifiers(): array
    {
        return ['foo'];
    }

    public function get(string $identifier): Config
    {
        if ($identifier === 'foo') {
            return Config::fromArray([
                'identifier' => 'foo',
                'parent_revision' => 'test',
                'data' => ['foo' => 'bar'],
            ]);
        }

        throw ConfigException::identifierNotFound($identifier);
    }
}
