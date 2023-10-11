<?php

namespace App\Tests\ChainCommandBundle;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ChainCommandFunctionalTest extends KernelTestCase
{
    private CommandTester $commandTester;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find('chain:command');
        $this->commandTester = new CommandTester($command);
    }

    public function testChainCommandFunctionality()
    {
        $this->commandTester->execute(['arguments' => ['foo:command', 'bar:command']]);
        $output = $this->commandTester->getDisplay();
        $this->assertEquals('Output from foo:command' . PHP_EOL . 'Output from bar:command', $output);
    }

    public function testChainCommandWithoutCommands()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No commands provided for chaining.');
        $this->commandTester->execute([]);
    }

    public function testChainCommandWithUnknownCommand()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown command provided for chaining.');
        $this->commandTester->execute(['arguments' => ['unknown:command']]);
    }

    public function testChainCommandWithFailingCommand()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(['arguments' => ['failing:command']]);
    }

    public function testChainCommandOrder()
    {
        $this->commandTester->execute(['arguments' => ['bar:command', 'foo:command']]);
        $output = $this->commandTester->getDisplay();
        $this->assertEquals('Output from bar:command' . PHP_EOL . 'Output from foo:command', $output);
    }
}
