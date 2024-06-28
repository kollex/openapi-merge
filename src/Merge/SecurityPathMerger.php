<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Merge;

use Mthole\OpenApiMerge\Util\Json;
use openapiphp\openapi\spec\OpenApi;

class SecurityPathMerger implements MergerInterface
{
    public function merge(
        OpenApi $existingSpec,
        OpenApi $newSpec,
    ): OpenApi {
        if (0 === \count($newSpec->security ?? [])) {
            return $existingSpec;
        }

        $clonedSpec = new OpenApi(Json::toArray($existingSpec->getSerializableData()));

        foreach ($newSpec->paths->getPaths() as $pathName => $path) {
            foreach ($path->getOperations() as $method => $operation) {
                if (null !== $operation->security) {
                    continue;
                }

                $path = $clonedSpec->paths->getPath($pathName);
                if (!isset($path->{$method}) || null === $path->{$method}) {
                    continue;
                }

                $path->{$method}->security = $newSpec->security;
            }
        }

        return $clonedSpec;
    }
}
