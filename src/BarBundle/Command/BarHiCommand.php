<?php

namespace App\BarBundle\Command;

use App\ChainCommandBundle\Interface\CommandChainerInterface;
use App\ChainCommandBundle\Service\CommandChainManager;
use App\FooBundle\Command\FooHelloCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use App\ChainCommandBundle\Traits\CommandChainingTrait;

/**
 * Class BarHiCommand
 * @package BarBundle\Command
 */
class BarHiCommand extends Command implements CommandChainerInterface
{
    use CommandChainingTrait;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    private $chainManager;

    protected function registerMasterCommand(string $commandName): void
    {
        // Store the master command name for the current command
        $this->chainManager->addCommandToChain($commandName, $this);
    }

    public function __construct(LoggerInterface $logger,CommandChainManager $chainManager)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->chainManager = $chainManager;
        echo 'BarHiCommand CommandChainManager ID: ' . spl_object_id($this->chainManager) . PHP_EOL;

        $this->registerMasterCommand(FooHelloCommand::class);
    }

    protected function configure(): void
    {
        $this->setName('bar:hi')->setDescription('Hi from Bar!');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->wasMasterCommandExecuted()) {
            $output->writeln('Error: You need to run foo:hello first.');
            return Command::FAILURE;
        }

        // Mark the current command as executed
        self::markCommandAsExecuted($this->getName());

        // Rest of your command logic
        $output->writeln('Hi from Bar!');
        return Command::SUCCESS;
    }

    public function addCommandToChain(string $mainCommandName, Command $chainedCommand): void
    {
        $this->chainManager->addCommandToChain($mainCommandName, $chainedCommand);

    }

    public function getChainedCommands(string $mainCommandName): array
    {
        //return ['foo:hello'];
        return $this->chainManager->getChainedCommands($mainCommandName);
    }
}
