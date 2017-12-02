<?php
declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\GenerateApiToken;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \App\Command\GenerateApiToken
 */
class GenerateApiTokenTest extends TestCase
{
    /**
     * @var GenerateApiToken
     */
    private $generate_api_token;

    protected function setUp()
    {
        $this->generate_api_token = new GenerateApiToken(__DIR__);
    }

    public function testExecute()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $this->generate_api_token->setHelperSet(new HelperSet([
            'question' => new class implements HelperInterface
            {
                public function setHelperSet(HelperSet $helperSet = null)
                {
                }

                public function getHelperSet()
                {
                }

                public function getName()
                {
                }

                public function ask()
                {
                    return 'FOOBAR';
                }
            }
        ]));
        $this->generate_api_token->run($input, $output);

        $output_string = $output->fetch();
        $regex = '/^Token has been generated: ([a-z0-9]{32})[\n\r]+This can now be used to access api calls.[\n\r]+$/';

        // cleanup
        preg_match($regex, $output_string, $matches);
        unlink(__DIR__ . '/' . $matches[1] . '.json');

        self::assertRegExp($regex, $output_string);
    }

    public function testExecuteEmptyName()
    {
        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $this->generate_api_token->setHelperSet(new HelperSet([
            'question' => new class implements HelperInterface
            {
                public function setHelperSet(HelperSet $helperSet = null)
                {
                }

                public function getHelperSet()
                {
                }

                public function getName()
                {
                }

                public function ask()
                {
                    return '';
                }
            }
        ]));
        $this->generate_api_token->run($input, $output);

        $output_string = $output->fetch();

        self::assertRegExp('/^No name given. Aborting.[\r\n]+$/', $output_string);
    }
}
