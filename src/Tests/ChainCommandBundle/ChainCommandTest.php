<?php

namespace App\Tests\ChainCommandBundle;

use App\ChainCommandBundle\Command\ChainCommand;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ChainCommandFunctionalTest extends KernelTestCase
{
    public function testChainCommandFunctionality()
    {
        $kernel = self::bootKernel();
        $commandTester = $kernel->getContainer()->get('test.service_container')->get('console.command_tester');

        $commandTester->execute(['command' => 'chain:command', 'arguments' => ['foo:command', 'bar:command']]);

        $output = $commandTester->getDisplay();

        $this->assertEquals(
            'Output from foo:command' . PHP_EOL . 'Output from bar:command',
            $output
        );
    }
}