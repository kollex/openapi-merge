<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge;

use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\FileHandling\SpecificationFile;
use Mthole\OpenApiMerge\Merge\MergerInterface;
use Mthole\OpenApiMerge\Merge\ReferenceNormalizer;
use Mthole\OpenApiMerge\Reader\FileReader;

class OpenApiMerge implements OpenApiMergeInterface
{
    /** @param list<MergerInterface> $merger */
    public function __construct(
        private readonly FileReader $openApiReader,
        /** @var MergerInterface[] $merger */
        private readonly array $merger,
        private readonly ReferenceNormalizer $referenceNormalizer,
    ) {
    }

    /** @param list<File> $additionalFiles */
    public function mergeFiles(File $baseFile, array $additionalFiles, bool $resolveReference = true): SpecificationFile
    {
        $mergedOpenApiDefinition = $this->openApiReader->readFile($baseFile, $resolveReference)->getOpenApi();

        // use "for" instead of "foreach" to iterate over new added files
        for ($i = 0; $i < \count($additionalFiles); ++$i) {
            $additionalFile = $additionalFiles[$i];
            $additionalDefinition = $this->openApiReader->readFile($additionalFile, $resolveReference)->getOpenApi();
            if (!$resolveReference) {
                $resolvedReferenceResult = $this->referenceNormalizer->normalizeInlineReferences(
                    $additionalFile,
                    $additionalDefinition,
                );
                array_push($additionalFiles, ...$resolvedReferenceResult->getFoundReferenceFiles());
                $additionalDefinition = $resolvedReferenceResult->getNormalizedDefinition();
            }

            foreach ($this->merger as $merger) {
                $mergedOpenApiDefinition = $merger->merge(
                    $mergedOpenApiDefinition,
                    $additionalDefinition,
                );
            }
        }

        if ($resolveReference && null !== $mergedOpenApiDefinition->components) {
            $mergedOpenApiDefinition->components->schemas = [];
        }

        return new SpecificationFile(
            $baseFile,
            $mergedOpenApiDefinition,
        );
    }
}
