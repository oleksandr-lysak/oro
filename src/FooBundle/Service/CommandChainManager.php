<?php
namespace App\FooBundle\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;

class CommandChainManager
{
    private $masterCommand;
    private array $chainedCommands = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param Command $command
     * @return void
     */
    public function setMasterCommand(Command $command): void
    {
        $this->masterCommand = $command;
        $this->logger->info(sprintf('%s is a master command of a command chain that has registered member commands', $command->getName()));
    }

    /**
     * @param Command $command
     * @return void
     */
    public function addChainedCommand(Command $command): void
    {
        $this->chainedCommands[] = $command;
        $this->logger->info(sprintf('%s registered as a member of %s command chain', $command->getName(), $this->masterCommand->getName()));
    }

    /**
     * @return array
     */
    public function getChainedCommands(): array
    {
        return $this->chainedCommands;
    }

}
