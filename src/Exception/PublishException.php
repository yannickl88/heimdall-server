<?php
declare(strict_types=1);

namespace App\Exception;

/**
 * Error code 2001-2999
 */
class PublishException extends ApiException
{
    public static function dataNotChanged(string $identifier): self
    {
        return new self(sprintf('Data has not changed for "%s".', $identifier), 2001);
    }

    public static function dataNotValid(array $extra_keys): self
    {
        return new self(sprintf('Fields "%s" are not supported.', implode('", "', $extra_keys)), 2002);
    }

    public static function revisionMismatch(string $identifier, string $current_revision, string $given_revision): self
    {
        return new self(sprintf('Revision mismatch for "%s", got "%s" but expected "%s".', $identifier, $given_revision, $current_revision), 2003);
    }
}
