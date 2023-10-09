<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
    App\FooBundle\FooBundle::class => ['all' => true],
    App\ChainCommandBundle\ChainCommandBundle::class => ['all' => true],
];
