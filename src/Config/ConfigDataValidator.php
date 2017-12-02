<?php
declare(strict_types=1);

namespace App\Config;

use App\Exception\ConfigException;
use App\Exception\PublishException;

class ConfigDataValidator
{
    private const ALLOWED_KEYS = ['description', 'includes', 'directives', 'env-variables', 'tasks'];

    public function validateIdentifier(string $identifier): void
    {
        if (1 !== preg_match('/^[a-z0-9][a-z0-9\.-_]*$/', $identifier)) {
            throw ConfigException::identifierBadFormat($identifier);
        }
    }

    public function validateData(array $data): void
    {
        $keys = array_keys($data);

        $extra_keys = array_diff($keys, self::ALLOWED_KEYS);

        if (count($extra_keys) > 0) {
            throw PublishException::dataNotValid($extra_keys);
        }
    }
}
