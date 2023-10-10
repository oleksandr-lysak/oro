<?php

namespace App\ChainCommandBundle\Traits;

trait CommandChainingTrait
{
    private static $executedCommands = [];
    private static $masterCommands = [];

    protected function registerMasterCommand(string $commandName): void
    {
        // Store the master command name for the current command
        self::$masterCommands[static::class] = $commandName;
    }

    protected function wasMasterCommandExecuted(): bool
    {
        // Check if the master command for the current command was executed
        return isset(self::$masterCommands[static::class]) && in_array(self::$masterCommands[static::class], self::$executedCommands, true);
    }

    public static function markCommandAsExecuted(string $commandName): void
    {
        // Mark a command as executed
        self::$executedCommands[] = $commandName;
    }
}
