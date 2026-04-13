<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@auto' => true,
        '@Symfony' => true,
        'phpdoc_to_comment' => false,
        'fully_qualified_strict_types' => false,
        'global_namespace_import' => [
            'import_classes' => true,
        ],
    ])
    ->setFinder(
        (new Finder())
            ->in(__DIR__)
    )
;
