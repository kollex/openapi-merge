<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge;

use cebe\openapi\spec\Paths;
use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\FileHandling\SpecificationFile;
use Mthole\OpenApiMerge\Reader\FileReader;

class OpenApiMerge implements OpenApiMergeInterface
{
    public function __construct(private FileReader $openApiReader)
    {
    }

    public function mergeFiles(File $baseFile, File ...$additionalFiles): SpecificationFile
    {
        $mergedOpenApiDefinition = $this->openApiReader->readFile($baseFile)->getOpenApi();

        foreach ($additionalFiles as $additionalFile) {
            $additionalDefinition = $this->openApiReader->readFile($additionalFile)->getOpenApi();

            $mergedOpenApiDefinition->paths = new Paths(
                array_merge(
                    $mergedOpenApiDefinition->paths->getPaths(),
                    $additionalDefinition->paths->getPaths(),
                ),
            );
        }

        if (null !== $mergedOpenApiDefinition->components) {
            $mergedOpenApiDefinition->components->schemas = [];
        }

        return new SpecificationFile(
            $baseFile,
            $mergedOpenApiDefinition,
        );
    }
}
