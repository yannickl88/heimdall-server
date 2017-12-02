<?php
declare(strict_types=1);

namespace App\Controller;

use App\Config\ConfigPublisher;
use App\Config\ConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/config")
 */
class ConfigController
{
    private $config_repository;
    private $config_publisher;

    public function __construct(ConfigRepository $config_repository, ConfigPublisher $config_publisher)
    {
        $this->config_repository = $config_repository;
        $this->config_publisher = $config_publisher;
    }

    /**
     * @Method("GET")
     * @Route("/identifiers", name="api.v1.config.identifiers")
     */
    public function identifiers(): JsonResponse
    {
        return new JsonResponse(['identifiers' => $this->config_repository->identifiers()]);
    }

    /**
     * @Method("GET")
     * @Route("/{identifier}", name="api.v1.config.item")
     */
    public function item(string $identifier): JsonResponse
    {
        return new JsonResponse(['config' => $this->config_repository->get($identifier)]);
    }

    /**
     * @Method("POST")
     * @Route("/{identifier}", name="api.v1.config.init")
     */
    public function init(string $identifier): JsonResponse
    {
        $config = $this->config_publisher->init($identifier);

        return new JsonResponse(['revision' => $config->getRevision()]);
    }

    /**
     * @Method("PUT")
     * @Route("/{identifier}", name="api.v1.config.publish")
     */
    public function update(Request $request, string $identifier): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $config = $this->config_publisher->publish($identifier, $data['parent_revision'], $data['data']);

        return new JsonResponse(['revision' => $config->getRevision()]);
    }
}
