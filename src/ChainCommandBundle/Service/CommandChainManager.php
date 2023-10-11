<?php

namespace App\ChainCommandBundle\Service;

use Symfony\Component\Console\Command\Command;
use Psr\Log\LoggerInterface;

/**
 * CommandChainManager manages command chains.
 *
 * This class provides methods to add commands to a chain, retrieve the list of chained commands, and manage the command chains.
 */
class CommandChainManager
{
    /**
     * @var array
     */
    private array $commandChains = [];

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * CommandChainManager constructor.
     *
     * Initializes the manager with a logger.
     *
     * @param LoggerInterface $logger Logger service.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Adds a command to the chain.
     *
     * This method allows a command to be added to a chain of commands that will be executed in sequence.
     *
     * @param string $mainCommandName The name of the main command to which the chained command will be added.
     * @param Command $chainedCommand The command that will be added to the chain.
     */
    public function addCommandToChain(string $mainCommandNameClass, string $mainCommandName, Command $chainedCommand): void
    {
        if (!isset($this->commandChains[$mainCommandNameClass])) {
            $this->commandChains[$mainCommandNameClass] = [];
            $this->logger->info(sprintf('%s is a master command of a command chain that has registered member commands', $mainCommandName,));
        }
        $this->commandChains[$mainCommandNameClass][] = $chainedCommand;
        $this->logger->info(sprintf('%s registered as a member of %s command chain',  $chainedCommand->getName(),$mainCommandName));
    }

    /**
     * Retrieves the commands chained to the specified main command.
     *
     * This method returns a list of commands that are chained to run after the specified main command.
     *
     * @param string $mainCommandName The name of the main command for which the chained commands are to be retrieved.
     * @return array List of chained commands.
     */
    public function getChainedCommands(string $mainCommandName): array
    {
        return $this->commandChains[$mainCommandName] ?? [];
    }
}
