<?php
namespace App\ChainCommandBundle\EventListener;

use App\ChainCommandBundle\Interface\CommandChainerInterface;
use App\ChainCommandBundle\Service\CommandChainManager;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Psr\Log\LoggerInterface;

/**
 * CommandChainListener listens to command execution events to handle command chaining.
 */
class CommandChainListener
{
    private $commandChainer;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * CommandChainListener constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger,CommandChainManager $chainManager)
    {
        $this->logger = $logger;
        $this->commandChainer = $chainManager;
    }

    /**
     * Handle the command before its execution.
     *
     * @param ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();
        $commandName = $command->getName();
        if ($command instanceof CommandChainerInterface) {
            foreach ($command->getChainedCommands($commandName) as $chainCommand) {
                $chainCommand->run($event->getInput(), $event->getOutput());
            }
        }

        $this->logger->info(sprintf('Executing %s command...', $commandName));
    }

    /**
     * Handle the command after its execution.
     *
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {

        $commandName = $event->getCommand()->getName();
        $chainedCommands = $this->commandChainer->getChainedCommands($commandName);

        foreach ($chainedCommands as $chainedCommand) {
            $chainedCommand->run($event->getInput(), $event->getOutput());
        }
    }
}
