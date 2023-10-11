<?php

namespace App\BarBundle\Command;

use App\ChainCommandBundle\Service\CommandChainManager;
use App\ChainCommandBundle\Service\ExecutedCommandsRegistry;
use App\FooBundle\Command\FooHelloCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use App\ChainCommandBundle\Traits\CommandChainingTrait;

/**
 * Class BarHiCommand
 *
 * This class represents a command that outputs a greeting from the Bar bundle.
 * It is part of a chain of commands and should be executed after the FooHelloCommand.
 *
 * @package BarBundle\Command
 */
class BarHiCommand extends Command
{
    use CommandChainingTrait;

    /**
     * @var string
     */
    private static string $commandName = 'bar:hi';

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
     * Registers the master command for the current command.
     *
     * @param string $className The name of the master command class.
     * @param string $commandName The name of the master command.
     */
    protected function registerMasterCommand(string $className, string $commandName): void
    {
        $this->chainManager->addCommandToChain($className, $commandName, $this);
    }

    /**
     * Constructor for the BarHiCommand class.
     *
     * @param LoggerInterface $logger Logger service.
     * @param CommandChainManager $chainManager Manages command chains.
     * @param ExecutedCommandsRegistry $registry Keeps track of executed commands.
     */
    public function __construct(LoggerInterface $logger,CommandChainManager $chainManager,ExecutedCommandsRegistry $registry)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->chainManager = $chainManager;
        $this->registry = $registry;

        $this->registerMasterCommand(FooHelloCommand::class,FooHelloCommand::getDefaultName());

    }

    /**
     * Configures the command.
     */
    protected function configure(): void
    {
        $this->setName($this::$commandName)->setDescription('Hi from Bar!');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input Input interface.
     * @param OutputInterface $output Output interface.
     * @return int Command exit status.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->registry->wasCommandExecuted(FooHelloCommand::getDefaultName())) {
            $output->writeln('Error: You need to run foo:hello first.');
            return Command::FAILURE;
        }

        $output->writeln('Hi from Bar!');
        return Command::SUCCESS;
    }

}
