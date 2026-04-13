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
    ])
    ->setFinder(
        (new Finder())
            ->in(__DIR__)
    )
;
