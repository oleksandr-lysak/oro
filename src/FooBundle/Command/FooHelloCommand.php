<?php

namespace App\FooBundle\Command;

use App\ChainCommandBundle\Service\CommandChainManager;
use App\ChainCommandBundle\Service\ExecutedCommandsRegistry;
use App\ChainCommandBundle\Traits\CommandChainingTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

/**
 * FooHelloCommand is a simple command that outputs a greeting message.
 *
 * This command is part of the FooBundle and provides functionality to greet the user.
 * @package FooBundle\Command
 */
class FooHelloCommand extends Command
{
    use CommandChainingTrait;

    /**
     * @var string
     */
    protected static $defaultName = 'foo:hello';

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var ExecutedCommandsRegistry
     */
    private ExecutedCommandsRegistry $registry;

    /**
     * @var CommandChainManager
     */
    private CommandChainManager $chainManager;

    /**
     * FooHelloCommand constructor.
     *
     * Initializes the command with a logger, a command chain manager, and a registry of executed commands.
     *
     * @param LoggerInterface $logger Logger service.
     * @param CommandChainManager $chainManager Manages command chains.
     * @param ExecutedCommandsRegistry $registry Keeps track of executed commands.
     */
    public function __construct(LoggerInterface $logger, CommandChainManager $chainManager, ExecutedCommandsRegistry $registry)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->chainManager = $chainManager;
        $this->registry = $registry;
    }

    /**
     * Configures the command.
     *
     * Sets the name, description, and help message for the command.
     */
    protected function configure() :void
    {
        $this
            ->setName($this::$defaultName)
            ->setDescription('Outputs a greeting message.')
            ->setHelp('This command allows you to output a greeting message...');
    }

    /**
     * Executes the command.
     *
     * Outputs a greeting message and logs the execution.
     *
     * @param InputInterface $input The input interface.
     * @param OutputInterface $output The output interface.
     * @return int The command exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = 'Hello from Foo!';
        $output->writeln($message);

        $this->logger->info($message);

        $this->registry->markCommandAsExecuted($this->getName());

        return Command::SUCCESS;
    }


}
