<?php
namespace App\FooBundle\Command;

use App\ChainCommandBundle\Interface\CommandChainerInterface;
use App\ChainCommandBundle\Service\CommandChainManager;
use App\ChainCommandBundle\Service\ExecutedCommandsRegistry;
use App\ChainCommandBundle\Traits\CommandChainingTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

/**
 * FooHelloCommand is a simple command that outputs a greeting message.
 * @package FooBundle\Command
 */
class FooHelloCommand extends Command implements CommandChainerInterface
{
    use CommandChainingTrait;

    protected static $defaultName = 'foo:hello';
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    private $registry;
    private CommandChainManager $chainManager;
    /**
     * FooHelloCommand constructor.
     *
     * @param LoggerInterface $logger
     * @param CommandChainManager $chainManager
     */
    public function __construct(LoggerInterface $logger,CommandChainManager $chainManager,ExecutedCommandsRegistry $registry)
    {
        parent::__construct();
        $this->logger = $logger;
        $this->chainManager = $chainManager;
        $this->registry = $registry;
    }

    /**
     * Configures the command.
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
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = 'Hello from Foo!';
        $output->writeln($message);
        $this->logger->info(sprintf('Executed command %s: %s', $this->getName(), $message));

        $this->registry->markCommandAsExecuted($this->getName());

        return Command::SUCCESS;
    }

    public function addCommandToChain(string $mainCommandName, Command $chainedCommand): void
    {
        $this->chainManager->addCommandToChain($mainCommandName, $chainedCommand);
    }

    public function getChainedCommands(string $mainCommandName): array
    {
        return $this->chainManager->getChainedCommands($mainCommandName);
    }
}
