<?php
namespace App\ChainCommandBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Exception\ExceptionInterface;

/**
 * Service responsible for managing command chains.
 */
class CommandChainService {
    private array $chains = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * @param string $masterCommand
     * @param string $chainedCommand
     * @return void
     */
    public function addCommandToChain(string $masterCommand, string $chainedCommand): void
    {
        if (!isset($this->chains[$masterCommand])) {
            $this->chains[$masterCommand] = [];
        }
        $this->chains[$masterCommand][] = $chainedCommand;
        $this->logger->info("$chainedCommand registered as a member of $masterCommand command chain");
    }

    /**
     * @return void
     */
    public function initializeChains() {
        $this->addCommandToChain('foo:hello', 'bar:hi');
    }


//    /**
//     * Registers a command as part of a chain.
//     *
//     * @param string $masterCommand   The main command name.
//     * @param string $chainedCommand  The command to be executed as part of the chain.
//     */
//    public function registerCommand(string $masterCommand, string $chainedCommand): void {
//        if (!isset($this->commands[$masterCommand])) {
//            $this->commands[$masterCommand] = [];
//        }
//        if (!in_array($chainedCommand, $this->commands[$masterCommand])) {
//            $this->commands[$masterCommand][] = $chainedCommand;
//            $this->logger->info("Command $chainedCommand registered to chain of $masterCommand");
//        } else {
//            $this->logger->warning("Command $chainedCommand is already registered in chain of $masterCommand");
//        }
//    }

    /**
     * @param ConsoleCommandEvent $event
     * @return void
     * @throws ExceptionInterface
     */
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $commandName = $event->getCommand()->getName();

        if ($this->isCommandChained($commandName)) {
            $masterCommand = $this->getMasterCommand($commandName);
            $event->getOutput()->writeln("Error: $commandName command is a member of $masterCommand command chain and cannot be executed on its own.");
            $event->disableCommand();
        } else {
            $chainedCommands = $this->getChainedCommands($commandName);
            foreach ($chainedCommands as $chainedCommand) {
                $event->getOutput()->writeln($chainedCommand);
                $this->logger->info("Executing $chainedCommand chain members:");
                $application = $event->getCommand()->getApplication();
                $application->find($chainedCommand)->run($event->getInput(), $event->getOutput());
            }
        }
    }

    /**
     * Retrieves all commands registered for a specific master command.
     *
     * @param string $masterCommand The main command name.
     * @return array The list of commands registered for the master command.
     */
    public function getChainedCommands(string $masterCommand): array {
        return $this->commands[$masterCommand] ?? [];
    }

    /**
     * @param string $command
     * @return bool
     */
    public function isCommandInChain(string $command): bool
    {
        foreach ($this->chains as $master => $chainedCommands) {
            if (in_array($command, $chainedCommands)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $chainedCommand
     * @return string|null
     */
    public function getMasterCommand(string $chainedCommand): ?string
    {
        foreach ($this->chains as $master => $chainedCommands) {
            if (in_array($chainedCommand, $chainedCommands)) {
                return $master;
            }
        }
        return null;
    }

    /**
     * Check if the given command name is a master command in any chain.
     *
     * @param string $commandName
     * @return bool
     */
    public function isMasterCommand(string $commandName): bool
    {
        return isset($this->commandChains[$commandName]);
    }
}
