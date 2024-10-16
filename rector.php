<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPhpVersion(PhpVersion::PHP_81)
    ->withFileExtensions([
        'php',
        'phtml',
    ])
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withSets([
        SetList::PHP_81,
        LevelSetList::UP_TO_PHP_81,
    ])
    ->withSkip([
        ReadOnlyPropertyRector::class,
    ])
    ->withImportNames(removeUnusedImports: true)
;
