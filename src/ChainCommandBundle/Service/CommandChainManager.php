<?php
namespace App\ChainCommandBundle\Service;

use Symfony\Component\Console\Command\Command;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CommandChainService handles the logic for chaining commands together.
 */
class CommandChainManager
{
    private array $commandChains = [];

    public function addCommandToChain(string $mainCommandName, Command $chainedCommand): void
    {

        if (!isset($this->commandChains[$mainCommandName])) {
            $this->commandChains[$mainCommandName] = [];
        }

        $this->commandChains[$mainCommandName][] = $chainedCommand;

    }

    public function getChainedCommands(string $mainCommandName): array
    {
        return $this->commandChains[$mainCommandName] ?? [];
    }


}
