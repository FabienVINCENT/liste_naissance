<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src');

$config = new PhpCsFixer\Config();

return $config
    ->setRiskyAllowed(true)
    ->setRules([
                   '@PSR12' => true,
                   '@Symfony' => true,
                   'array_syntax' => ['syntax' => 'short'],
               ])
    ->setFinder($finder);
