<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Merge;

use Mthole\OpenApiMerge\FileHandling\File;
use openapiphp\openapi\spec\OpenApi;

final readonly class ReferenceResolverResult
{
    public function __construct(
        private OpenApi $openApiSpecification,
        /** @var list<File> $foundReferenceFiles */
        private array $foundReferenceFiles,
    ) {
    }

    public function getNormalizedDefinition(): OpenApi
    {
        return $this->openApiSpecification;
    }

    /** @return list<File> */
    public function getFoundReferenceFiles(): array
    {
        return $this->foundReferenceFiles;
    }
}
