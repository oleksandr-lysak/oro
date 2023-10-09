<?php
namespace App\ChainCommandBundle\EventListener;

use App\ChainCommandBundle\Service\CommandChainService;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;


/**
 * Listener responsible for executing command chains.
 */
class CommandChainListener {
    private CommandChainService $commandChainService;
    private LoggerInterface $logger;

    /**
     * CommandChainListener constructor.
     *
     * @param CommandChainService $commandChainService The command chain service.
     * @param $logger LoggerInterface The logger service.
     */
    public function __construct(CommandChainService $commandChainService, LoggerInterface $logger) {
        $this->commandChainService = $commandChainService;
        $this->logger = $logger;
    }

    /**
     * Event handler for console command execution. Executes chained commands if any.
     *
     * @param ConsoleCommandEvent $event The console command event.
     * @throws ExceptionInterface
     */
    public function onConsoleCommand(ConsoleCommandEvent $event): void
    {
        $commandName = $event->getCommand()->getName();
        $chainedCommands = $this->commandChainService->getChainedCommands($commandName);

        if (empty($chainedCommands)) {
            return;
        }

        $this->logger->info("$commandName is a master command of a command chain that has registered member commands");

        foreach ($chainedCommands as $chainedCommand) {
            $this->logger->info("$chainedCommand registered as a member of $commandName command chain");
        }

        $this->logger->info("Executing $commandName command itself first:");
        $event->getCommand()->run($event->getInput(), $event->getOutput());

        $this->logger->info("Executing $commandName chain members:");
        $application = $event->getCommand()->getApplication();

        foreach ($chainedCommands as $chainedCommand) {
            try {
                $application->find($chainedCommand)->run($event->getInput(), $event->getOutput());
            } catch (ExceptionInterface $e) {
                $this->logger->error("Error executing chained command: $chainedCommand. Error: " . $e->getMessage());
            }
        }

        $this->logger->info("Execution of $commandName chain completed");
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $this->commandChainService->initializeChains();
    }


}
