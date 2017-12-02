<?php
declare(strict_types=1);

namespace App\Exception;

/**
 * Error code 1001-1999
 */
class ConfigException extends ApiException
{
    public static function identifierBadFormat(string $identifier): self
    {
        return new self(sprintf('Identifier "%s" has a bad format.', $identifier), 1001);
    }

    public static function identifierNotFound(string $identifier, \Throwable $previous = null): self
    {
        return new self(sprintf('Unknown identifier "%s".', $identifier), 1002, $previous);
    }

    public static function identifierAlreadyExists(string $identifier): self
    {
        return new self(sprintf('Identifier "%s" already exists.', $identifier), 1003);
    }
}
