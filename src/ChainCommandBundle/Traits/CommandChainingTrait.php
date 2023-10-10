<?php

namespace App\ChainCommandBundle\Traits;

trait CommandChainingTrait
{

    private static $masterCommands = [];

    protected function registerMasterCommand(string $commandName): void
    {
        // Store the master command name for the current command
        self::$masterCommands[static::class] = $commandName;
    }

}
