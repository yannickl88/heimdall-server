<?php
declare(strict_types=1);

namespace App\Config\Data;

use App\Exception\NoChangeException;

class Config implements \JsonSerializable
{
    private $identifier;
    private $data;
    private $parent_revision;

    public static function init(string $identifier): self
    {
        return new self($identifier, [], '');
    }

    public static function newRevision(Config $config, array $data): self
    {
        $revision = new self($config->identifier, $data, $config->getRevision());

        if (json_encode($revision->data) === json_encode($config->data)) {
            throw new NoChangeException('New revision is the same as previous.');
        }

        return $revision;
    }

    public static function fromArray(array $data): self
    {
        return new self($data['identifier'], $data['data'], $data['parent_revision']);
    }

    private function __construct(string $identifier, array $data, string $parent_revision)
    {
        $this->identifier = $identifier;
        $this->data = $data;
        $this->parent_revision = $parent_revision;
    }

    public function getRevision(): string
    {
        return md5($this->identifier . '|' . $this->parent_revision . '|' . json_encode($this->data));
    }

    public function jsonSerialize(): array
    {
        return [
            'identifier' => $this->identifier,
            'parent_revision' => $this->parent_revision,
            'revision' => $this->getRevision(),
            'data' => $this->data
        ];
    }
}
