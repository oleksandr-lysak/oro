<?php

namespace App\ChainCommandBundle\Traits;

trait CommandChainingTrait
{
    private static $executedCommands = [];

    protected function registerMasterCommand(string $commandName): void
    {
        // Store the master command name for the current command
        self::$executedCommands[static::class] = $commandName;
    }

    protected function wasMasterCommandExecuted(): bool
    {
        // Check if the master command for the current command was executed
        return in_array(self::$executedCommands[static::class], self::$executedCommands, true);
    }

    public static function markCommandAsExecuted(string $commandName): void
    {
        // Mark a command as executed
        self::$executedCommands[] = $commandName;
    }
}

