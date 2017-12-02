<?php
declare(strict_types=1);

namespace App\Config;

use App\Config\Data\Config;
use App\Exception\ConfigException;

class ConfigRepository
{
    private $validator;
    private $configs_dir;

    public function __construct(ConfigDataValidator $validator, string $configs_dir)
    {
        $this->validator = $validator;
        $this->configs_dir = $configs_dir;
    }

    public function identifiers(): array
    {
        $files = glob($this->configs_dir . '/*.json');

        return array_map(function (string $file) {
            return basename($file, '.json');
        }, $files);
    }

    public function get(string $identifier): Config
    {
        $this->validator->validateIdentifier($identifier);

        $file = $this->configs_dir . '/' . $identifier . '.json';

        if (!file_exists($file)) {
            throw ConfigException::identifierNotFound($identifier);
        }

        $data = json_decode(file_get_contents($file), true);

        return Config::fromArray($data);
    }
}
