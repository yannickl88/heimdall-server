<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class IndexController
{
    public function __invoke()
    {
        return new Response('<html><head><title>Heimdall</title></head><body><h1>Heimdall Config Server online!</h1></body></html>');
    }
}
