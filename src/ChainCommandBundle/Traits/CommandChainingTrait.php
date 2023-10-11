<?php

namespace App\ChainCommandBundle\Traits;

/**
 * CommandChainingTrait provides functionality for command chaining.
 *
 * This trait allows commands to register a master command, which can be used to establish a chain of commands.
 */
trait CommandChainingTrait
{
    /**
     * @var array
     */
    private static array $masterCommands = [];

    /**
     * Registers a master command for the current command.
     *
     * This method allows a command to specify its master command, which can be used to establish a chain of commands.
     *
     * @param string $commandName The name of the master command to register.
     */
    protected function registerMasterCommand(string $commandName): void
    {
        self::$masterCommands[static::class] = $commandName;
    }
}
