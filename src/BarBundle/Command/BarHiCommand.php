<?php

namespace App\BarBundle\Command;

use App\ChainCommandBundle\Service\CommandChainManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BarHiCommand
 * @package BarBundle\Command
 */
class BarHiCommand extends Command
{
    protected static $defaultName = 'bar:hi';
    private CommandChainManager $commandChainManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(CommandChainManager $chainCommandManager, LoggerInterface $logger)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->commandChainManager = $chainCommandManager;

    }

    protected function configure(): void
    {
        $this->setDescription('Hi from Bar!');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->commandChainManager->isCommandChained(self::$defaultName)) {
            $output->writeln('Error: bar:hi command is a member of foo:hello command chain and cannot be executed on its own.');
            return Command::FAILURE;
        }

        $output->writeln('Hi from Bar!');
        $this->logger->info('Hi from Bar!');
        return Command::SUCCESS;
    }
}
