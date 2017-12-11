<?php
declare(strict_types=1);

namespace App\Config;

use App\Config\Data\Config;
use App\Config\ConfigDataValidator;
use App\Exception\ConfigException;
use App\Exception\NoChangeException;
use App\Exception\PublishException;

class ConfigPublisher
{
    private $config_repository;
    private $validator;
    private $configs_dir;
    private $revisions_dir;

    public function __construct(ConfigRepository $config_repository, ConfigDataValidator $validator, string $configs_dir, string $revisions_dir)
    {
        $this->config_repository = $config_repository;
        $this->validator = $validator;
        $this->configs_dir = $configs_dir;
        $this->revisions_dir = $revisions_dir;
    }

    /**
     * @throws PublishException
     */
    public function init(string $identifier): Config
    {
        $this->validator->validateIdentifier($identifier);

        try {
            $this->config_repository->get($identifier);
        } catch (ConfigException $e) {
            $config = Config::init($identifier);

            $this->save($identifier, $config);

            return $config;
        }

        throw ConfigException::identifierAlreadyExists($identifier);
    }

    /**
     * @throws PublishException
     */
    public function publish(string $identifier, string $parent_revision, array $data): Config
    {
        $this->validator->validateIdentifier($identifier);
        $this->validator->validateData($data);

        try {
            $current = $this->config_repository->get($identifier);
        } catch (ConfigException $e) {
            throw ConfigException::identifierNotFound($identifier, $e);
        }

        if ($parent_revision !== $current->getRevision()) {
            throw PublishException::revisionMismatch($identifier, $current->getRevision(), $parent_revision);
        }

        try {
            $config = Config::newRevision($current, $data);
        } catch (NoChangeException $e) {
            throw PublishException::dataNotChanged($identifier);
        }

        $this->save($identifier, $config);

        return $config;
    }

    private function save(string $identifier, Config $config): void
    {
        if (!file_exists($this->configs_dir) && !mkdir($this->configs_dir) && !is_dir($this->configs_dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->configs_dir));
        }
        if (!file_exists($this->revisions_dir) && !mkdir($this->revisions_dir) && !is_dir($this->revisions_dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->revisions_dir));
        }

        $file = $this->configs_dir . '/' . $identifier . '.json';

        file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT));

        // make a revision
        copy($file, $this->revisions_dir . '/' . $identifier . '.' . $config->getRevision() . '.json');
    }
}
