<?php

namespace App\ChainCommandBundle\EventListener;

use App\ChainCommandBundle\Service\CommandChainManager;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * CommandChainListener listens to command execution events to handle command chaining.
 *
 * This listener is responsible for executing chained commands before and after the main command.
 */
class CommandChainListener
{
    /**
     * @var CommandChainManager
     */
    private CommandChainManager $chainManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * CommandChainListener constructor.
     *
     * Initializes the listener with a logger and a command chain manager.
     *
     * @param LoggerInterface $logger Logger service.
     * @param CommandChainManager $chainManager Manages command chains.
     */
    public function __construct(LoggerInterface $logger, CommandChainManager $chainManager)
    {
        $this->logger = $logger;
        $this->chainManager = $chainManager;
    }

    /**
     * Handle the command before its execution.
     *
     * If the command is part of a chain, this method will execute all the chained commands before the main command.
     *
     * @param ConsoleCommandEvent $event The console command event.
     */
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {

    }

    /**
     * Handle the command after its execution.
     *
     * Executes any commands that are chained to run after the main command.
     *
     * @param ConsoleTerminateEvent $event The console terminate event.
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $commandName = get_class($event->getCommand());
        $chainedCommands = $this->chainManager->getChainedCommands($commandName);
        $this->logger->info(sprintf('Executing %s chain members:', $event->getInput()));

        foreach ($chainedCommands as $chainedCommand) {
            $bufferedOutput = new BufferedOutput();
            $chainedCommand->run($event->getInput(), $bufferedOutput);

            $outputText = $bufferedOutput->fetch();
            $event->getOutput()->write($outputText);
            $this->logger->info($outputText);
        }
        $this->logger->info(sprintf('Execution of %s  chain completed.',$event->getInput()));
    }
}
