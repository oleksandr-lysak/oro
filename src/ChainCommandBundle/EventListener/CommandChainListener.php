<?php
namespace App\ChainCommandBundle\EventListener;

use App\ChainCommandBundle\Service\CommandChainManager;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Psr\Log\LoggerInterface;

/**
 * CommandChainListener listens to command execution events to handle command chaining.
 */
class CommandChainListener
{
    private $chainManager;
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
        $this->chainManager = $chainManager;
    }

    /**
     * Handle the command before its execution.
     *
     * @param ConsoleCommandEvent $event
     */
//    public function onConsoleCommand(ConsoleCommandEvent $event): void
//    {
//        $command = $event->getCommand();
//        $commandName = $command->getName();
//        if ($command instanceof CommandChainerInterface) {
//            foreach ($command->getChainedCommands($commandName) as $chainCommand) {
//                $chainCommand->run($event->getInput(), $event->getOutput());
//            }
//        }
//
//        $this->logger->info(sprintf('Executing %s command...', $commandName));
//    }

    /**
     * Handle the command after its execution.
     *
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event): void
    {
        $commandName = get_class($event->getCommand());
        $chainedCommands = $this->chainManager->getChainedCommands($commandName);

        foreach ($chainedCommands as $chainedCommand) {
            $chainedCommand->run($event->getInput(), $event->getOutput());
        }
    }
}
