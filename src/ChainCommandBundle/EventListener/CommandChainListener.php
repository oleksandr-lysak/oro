<?php
namespace App\ChainCommandBundle\EventListener;

use App\ChainCommandBundle\Service\CommandChainService;
use Symfony\Component\Console\Application;
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
    private Application $application;


    /**
     * @param CommandChainService $commandChainService
     * @param LoggerInterface $logger
     * @param Application $application
     */
    public function __construct(CommandChainService $commandChainService, LoggerInterface $logger, Application $application) {
        $this->commandChainService = $commandChainService;
        $this->logger = $logger;
        $this->application = $application;
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
        // if command in chain
        if ($this->commandChainService->isCommandInChain($commandName)) {
            $event->disableCommand();
            $this->logger->error(sprintf('Error: %s command is a member of %s command chain and cannot be executed on its own.', $commandName, $this->commandChainService->getMasterCommand($commandName)));
            return;
        }

        // if command is master command of chain
        if ($this->commandChainService->isMasterCommand($commandName)) {
            // Execute all command after master command
            $chainCommands = $this->commandChainService->getChainedCommands($commandName);
            foreach ($chainCommands as $chainCommand) {
                $this->application->find($chainCommand)->run($event->getInput(), $event->getOutput());
            }
        }

        $this->logger->info("Execution of $commandName chain completed");
    }

    public function onKernelRequest(RequestEvent $event) :void
    {
        $this->commandChainService->initializeChains();
    }


}
