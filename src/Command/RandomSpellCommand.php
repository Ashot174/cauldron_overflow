<?php

namespace App\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RandomSpellCommand extends Command
{
    protected static $defaultName = 'app:random-spell';
    protected static $defaultDescription = 'Cast a random spell';
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, string $name = null)
    {
        parent::__construct($name);
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('your-name', InputArgument::OPTIONAL, 'Your name')
            ->addOption('yell', null, InputOption::VALUE_NONE, 'Yell?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $yourName = $input->getArgument('your-name');

        if ($yourName) {
            $io->note(sprintf('Hi %s!', $yourName));
        }

        $spells = [
            'alohomora',
            'confundo',
            'engorgio',
            'expecto patronum',
            'expellarmius',
            'impedimenta',
            'reparo'
        ];

        $spell = $spells[array_rand($spells)];

        if ($input->getOption('yell')) {
            $spell = strtoupper($spell);
        }

        $this->logger->info('Casting spell ' . $spell);

        $io->success($spell);

        return Command::SUCCESS;
    }
}
