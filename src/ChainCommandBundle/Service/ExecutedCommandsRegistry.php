<?php
namespace App\ChainCommandBundle\Service;

class ExecutedCommandsRegistry
{
    private array $executedCommands = [];

    public function markCommandAsExecuted(string $commandName): void
    {
        $this->executedCommands[$commandName] = true;
        //dd($this->executedCommands);
    }

    public function wasCommandExecuted(string $commandName): bool
    {
        return isset($this->executedCommands[$commandName]);
    }
}

