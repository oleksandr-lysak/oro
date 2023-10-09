<?php
namespace App\FooBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\FooBundle\Service\CommandChainManager;

class FooHelloCommand extends Command
{
    protected static $defaultName = 'foo:hello';
    private CommandChainManager $commandChainManager;

    public function __construct(CommandChainManager $commandChainManager)
    {
        parent::__construct();
        $this->commandChainManager = $commandChainManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Hello from FooBundle command.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Hello from Foo!');
        $this->commandChainManager->getLogger()->info('Hello from Foo!');
        foreach ($this->commandChainManager->getChainedCommands() as $chainedCommand) {
            $chainedCommand->run($input, $output);
        }
        return Command::SUCCESS;
    }
}
