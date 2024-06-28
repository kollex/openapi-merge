<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Writer;

use Mthole\OpenApiMerge\FileHandling\SpecificationFile;
use Mthole\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use openapiphp\openapi\Writer;

/**
 * @see \Mthole\OpenApiMerge\Tests\Writer\DefinitionWriterTest
 */
final class DefinitionWriter implements DefinitionWriterInterface
{
    public function write(SpecificationFile $specFile): string
    {
        return match ($specFile->getFile()->getFileExtension()) {
            'json' => $this->writeToJson($specFile),
            'yml', 'yaml' => $this->writeToYaml($specFile),
            default => throw InvalidFileTypeException::createFromExtension($specFile->getFile()->getFileExtension()),
        };
    }

    public function writeToJson(SpecificationFile $specFile): string
    {
        return Writer::writeToJson(
            $specFile->getOpenApi(),
            \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
        );
    }

    public function writeToYaml(SpecificationFile $specFile): string
    {
        return Writer::writeToYaml($specFile->getOpenApi());
    }
}
