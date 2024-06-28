<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Reader;

use openapiphp\openapi\Reader;
use openapiphp\openapi\SpecObjectInterface;

/**
 * @see \Mthole\OpenApiMerge\Tests\Reader\OpenApiReaderWrapperTest
 */
class OpenApiReaderWrapper
{
    /**
     * @phpstan-param class-string<T> $baseType
     *
     * @phpstan-return T
     *
     * @phpstan-template T of SpecObjectInterface
     */
    public function readFromYamlFile(string $fileName, string $baseType, bool $resolveReferences): SpecObjectInterface
    {
        return Reader::readFromYamlFile($fileName, $baseType, $resolveReferences);
    }

    /**
     * @phpstan-param class-string<T> $baseType
     *
     * @phpstan-return T
     *
     * @phpstan-template T of SpecObjectInterface
     */
    public function readFromJsonFile(string $fileName, string $baseType, bool $resolveReferences): SpecObjectInterface
    {
        return Reader::readFromJsonFile($fileName, $baseType, $resolveReferences);
    }
}
