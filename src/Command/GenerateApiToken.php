<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GenerateApiToken extends Command
{
    private $tokens_dir;

    public function __construct(string $tokens_dir)
    {
        parent::__construct('app:generate-key');

        $this->tokens_dir = $tokens_dir;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var $helper QuestionHelper */
        $helper = $this->getHelper('question');

        $question = new Question('Application/Username for this token: ');
        $username = $helper->ask($input, $output, $question);

        if (empty($username)) {
            $output->writeln('No name given. Aborting.');
            return;
        }

        do {
            $token = bin2hex(random_bytes(16));

            $file = $this->tokens_dir . '/' . $token . '.json';
        } while (file_exists($file));

        file_put_contents($file, json_encode([
            'username' => $username
        ], JSON_PRETTY_PRINT));

        $output->writeln('Token has been generated: ' . $token);
        $output->writeln('This can now be used to access api calls.');
    }
}
