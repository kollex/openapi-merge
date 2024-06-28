<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Reader;

use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\FileHandling\SpecificationFile;
use Mthole\OpenApiMerge\Reader\Exception\InvalidFileTypeException;
use openapiphp\openapi\spec\OpenApi;

final class FileReader
{
    private readonly OpenApiReaderWrapper $openApiReader;

    public function __construct(OpenApiReaderWrapper $openApiReader = null)
    {
        $this->openApiReader = $openApiReader ?? new OpenApiReaderWrapper();
    }

    public function readFile(File $inputFile, bool $resolveReferences = true): SpecificationFile
    {
        $spec = match ($inputFile->getFileExtension()) {
            'yml', 'yaml' => $this->openApiReader->readFromYamlFile(
                $inputFile->getAbsoluteFile(),
                OpenApi::class,
                $resolveReferences,
            ),
            'json' => $this->openApiReader->readFromJsonFile(
                $inputFile->getAbsoluteFile(),
                OpenApi::class,
                $resolveReferences,
            ),
            default => throw InvalidFileTypeException::createFromExtension($inputFile->getFileExtension()),
        };

        return new SpecificationFile(
            $inputFile,
            $spec,
        );
    }
}
