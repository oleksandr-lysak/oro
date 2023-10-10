<?php

namespace App\ChainCommandBundle\Service;

use Symfony\Component\Console\Command\Command;
use Psr\Log\LoggerInterface;

class CommandChainManager
{
    private array $commandChains = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function addCommandToChain(string $mainCommandName, Command $chainedCommand): void
    {
        if (!isset($this->commandChains[$mainCommandName])) {
            $this->commandChains[$mainCommandName] = [];
        }

        $this->commandChains[$mainCommandName][] = $chainedCommand;
        $this->logger->info(sprintf('Added command %s to chain for %s', $chainedCommand->getName(), $mainCommandName));
    }

    public function getChainedCommands(string $mainCommandName): array
    {
        $commands = $this->commandChains[$mainCommandName] ?? [];
        foreach ($commands as $command) {
            $this->logger->info(sprintf('Chained command for %s: %s', $mainCommandName, $command->getName()));
        }
        return $commands;
    }

    public function getId(): string
    {
        return spl_object_hash($this);
    }
}
