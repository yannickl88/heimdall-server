<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Config\ConfigPublisher;
use App\Config\ConfigRepository;
use App\Kernel;
use App\Security\ApiUserProvider;
use Psr\Log\NullLogger;

class TestKernel extends Kernel
{
    public function buildContainer()
    {
        $container = parent::buildContainer();

        $container->register(TestConfigRepository::class)
            ->setDecoratedService(ConfigRepository::class);

        $container->register(TestConfigPublisher::class)
            ->setDecoratedService(ConfigPublisher::class);

        $container->register(TestApiUserProvider::class)
            ->setDecoratedService(ApiUserProvider::class);

        $container->register(NullLogger::class)
            ->setDecoratedService('logger');

        return $container;
    }
}
