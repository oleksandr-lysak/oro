<?php

namespace App\ChainCommandBundle\Service;

/**
 * ExecutedCommandsRegistry keeps track of executed commands.
 *
 * This class provides methods to mark commands as executed and check if a command has been executed.
 */
class ExecutedCommandsRegistry
{
    /**
     * @var array
     */
    private array $executedCommands = [];

    /**
     * Marks a command as executed.
     *
     * This method allows a command to be marked as executed, so that it can be tracked and checked later.
     *
     * @param string $commandName The name of the command to mark as executed.
     */
    public function markCommandAsExecuted(string $commandName): void
    {
        $this->executedCommands[$commandName] = true;
    }

    /**
     * Checks if a command has been executed.
     *
     * This method returns a boolean indicating whether a specific command has been marked as executed.
     *
     * @param string $commandName The name of the command to check.
     * @return bool True if the command has been executed, false otherwise.
     */
    public function wasCommandExecuted(string $commandName): bool
    {
        return isset($this->executedCommands[$commandName]);
    }
}
