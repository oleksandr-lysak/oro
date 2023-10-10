<?php
namespace App\ChainCommandBundle\Service;

use App\ChainCommandBundle\Interface\CommandChainerInterface;
use Symfony\Component\Console\Command\Command;

class CommandChainer implements CommandChainerInterface
{
    private $commandChains = [];

    public function addCommandToChain(string $mainCommandName, Command $chainedCommand): void
    {
        $this->commandChains[$mainCommandName][] = $chainedCommand;
    }

    public function getChainedCommands(string $mainCommandName): array
    {
        return $this->commandChains[$mainCommandName] ?? [];
    }
}
