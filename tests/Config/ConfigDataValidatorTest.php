<?php
declare(strict_types=1);

namespace App\Tests\Config;

use App\Config\ConfigDataValidator;
use App\Exception\ConfigException;
use App\Exception\PublishException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Config\ConfigDataValidator
 */
class ConfigDataValidatorTest extends TestCase
{
    /**
     * @var ConfigDataValidator
     */
    private $config_data_validator;

    protected function setUp()
    {
        $this->config_data_validator = new ConfigDataValidator();
    }

    /**
     * @dataProvider identifierProvider
     */
    public function testValidateIdentifier($has_error, $identifier)
    {
        if ($has_error) {
            $this->expectException(ConfigException::class);
        }

        $this->config_data_validator->validateIdentifier($identifier);

        // dummy so it asserts something
        self::assertTrue(true);
    }

    public static function identifierProvider()
    {
        return [
            [false, 'foo.bar'],
            [true, '.foo.bar'],
            [true, './foo.bar'],
            [true, '../foo.bar'],
            [true, '/foo.bar'],
            [true, '"foo.bar'],
            [true, '\'foo.bar'],
            [true, 'foo."bar'],
            [true, 'foo.\'bar'],
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testValidateData($has_error, $data)
    {
        if ($has_error) {
            $this->expectException(PublishException::class);
        }

        $this->config_data_validator->validateData($data);

        // dummy so it asserts something
        self::assertTrue(true);
    }

    public static function dataProvider()
    {
        return [
            [false, ['description' => [], 'includes' => [], 'directives' => [], 'env-variables' => [], 'tasks' => []]],
            [false, []],
            [true, ['foobar' => []]],
        ];
    }
}
