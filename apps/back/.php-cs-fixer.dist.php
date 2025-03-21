<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['var', 'vendor']);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'phpdoc_to_comment' => false,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_summary' => false,
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last',
            'sort_algorithm' => 'none',
        ],
    ])
    ->setFinder($finder)
;
