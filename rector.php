<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonyLevelSetList;
use Rector\Symfony\Set\SymfonySetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            __DIR__ . '/src',
            __DIR__ . '/tests',
        ]
    );
    $rectorConfig->sets(
        [
            LevelSetList::UP_TO_PHP_82,
            SymfonyLevelSetList::UP_TO_SYMFONY_63,
            SymfonySetList::SYMFONY_CODE_QUALITY,
            SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
        ]
    );

    $rectorConfig->skip(
        [
            FinalizePublicClassConstantRector::class,
        ]
    );
};
