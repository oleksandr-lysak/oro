<?php
namespace App\ChainCommandBundle\Interface;

use App\ChainCommandBundle\Service\CommandChainer;
use Symfony\Component\Console\Command\Command;

interface CommandChainerInterface
{
    public function addCommandToChain(string $mainCommandName, Command $chainedCommand): void;
    public function getChainedCommands(string $mainCommandName): array;
}
