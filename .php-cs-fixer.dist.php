<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'bin',
        'var',
        'vendor',
    ])
    ->name('*.php');
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_whitespace_in_blank_line' => true,
        'no_trailing_whitespace' => true,
        'line_ending'  => true,
        'single_quote' => true,
        'global_namespace_import' =>  ['import_classes' => true, 'import_functions' => true],
        'declare_strict_types' => true,
        'phpdoc_to_comment' => ['ignored_tags' => ['psalm-suppress', 'phpstan-ignore', 'var'],],
    ])
    ->setFinder($finder)
;
