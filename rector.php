<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeFromPropertyTypeRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withSkip([
        AddParamTypeFromPropertyTypeRector::class => [
            __DIR__.'/src/GlowmarktApi.php',
        ],
        ClassPropertyAssignToConstructorPromotionRector::class => [
            __DIR__.'/src/GlowmarktApi.php',
        ],
    ])
    ->withPhpSets()
    ->withImportNames()
    ->withComposerBased()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
    )
    ->withRules([
        DeclareStrictTypesRector::class,
    ])
;
