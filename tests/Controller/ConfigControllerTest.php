<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @covers \App\Controller\ConfigController
 */
class ConfigControllerTest extends WebTestCase
{
    public function testApiV1ConfigIdentifiers()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/config/identifiers?token=testtoken');

        self::assertSame(
            '{"identifiers":["foo"]}',
            $client->getResponse()->getContent()
        );
    }

    public function testApiV1ConfigFoo()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/config/foo?token=testtoken');

        self::assertSame(
            '{"config":{"identifier":"foo","parent_revision":"test","revision":"d2b1752084e0f592ef37d9a0c4e4e739","data":{"foo":"bar"}}}',
            $client->getResponse()->getContent()
        );
    }

    public function testApiV1ConfigInit()
    {
        $client = static::createClient();
        $client->request('POST', '/api/v1/config/bar?token=testtoken');

        self::assertSame(
            '{"revision":"9371bf2eedef0b82da1b18407c91a14c"}',
            $client->getResponse()->getContent()
        );
    }

    public function testApiV1ConfigUpdate()
    {
        $data = ['parent_revision' => 'phpunit', 'data' => ['foo' => 'baz']];

        $client = static::createClient();
        $client->request('PUT', '/api/v1/config/foo?token=testtoken', [], [], [], json_encode($data));

        self::assertSame(
            '{"revision":"558bbf70530287f3b67ae4657bd6cb07"}',
            $client->getResponse()->getContent()
        );
    }
}
