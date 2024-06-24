<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Merge;

use Mthole\OpenApiMerge\FileHandling\File;
use cebe\openapi\spec\MediaType;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Reference;
use cebe\openapi\spec\Schema;

use function array_map;
use function assert;
use function count;
use function preg_match;

use const DIRECTORY_SEPARATOR;

class ReferenceNormalizer
{
    public function normalizeInlineReferences(
        File $openApiFile,
        OpenApi $openApiDefinition,
    ): ReferenceResolverResult {
        $refFileCollection = [];
        foreach ($openApiDefinition->paths as $path) {
            foreach ($path->getOperations() as $operation) {
                assert($operation->responses !== null);
                foreach ($operation->responses->getResponses() as $statusCode => $response) {
                    if ($response === null) {
                        continue;
                    }

                    if ($response instanceof Reference) {
                        $operation->responses->addResponse(
                            (string) $statusCode,
                            $this->normalizeReference($response, $refFileCollection),
                        );
                        continue;
                    }

                    foreach ($response->content as $responseContent) {
                        assert($responseContent instanceof MediaType);
                        if ($responseContent->schema instanceof Reference) {
                            $responseContent->schema = $this->normalizeReference(
                                $responseContent->schema,
                                $refFileCollection,
                            );
                        }

                        if ($responseContent->schema instanceof Schema) {
                            $schemaProperties = $responseContent->schema->properties ?? [];
                            foreach ($schemaProperties as $propertyName => $property) {
                                if (! ($property instanceof Reference)) {
                                    continue;
                                }

                                $schemaProperties[$propertyName] = $this->normalizeReference(
                                    $property,
                                    $refFileCollection,
                                );
                            }

                            if ($schemaProperties !== []) {
                                $responseContent->schema->properties = $schemaProperties;
                            }
                        }

                        $newExamples = [];
                        foreach ($responseContent->examples as $key => $example) {
                            if ($example instanceof Reference) {
                                $newExamples[$key] = $this->normalizeReference(
                                    $example,
                                    $refFileCollection,
                                );
                            } else {
                                $newExamples[$key] = $example;
                            }
                        }

                        if (count($newExamples) <= 0) {
                            continue;
                        }

                        $responseContent->examples = $newExamples;
                    }
                }
            }
        }

        return new ReferenceResolverResult(
            $openApiDefinition,
            $this->normalizeFilePaths($openApiFile, $refFileCollection),
        );
    }

    /** @param list<string> $refFileCollection */
    private function normalizeReference(Reference $reference, array &$refFileCollection): Reference
    {
        $matches       = [];
        $referenceFile = $reference->getReference();
        if (preg_match('~^(?<referenceFile>.*)(?<referenceString>#/.*)~', $referenceFile, $matches) === 1) {
            $refFile = $matches['referenceFile'];

            if ($refFile !== '') {
                $refFileCollection[] = $refFile;
            }

            return new Reference(['$ref' => $matches['referenceString']]);
        }

        return $reference;
    }

    /**
     * @param list<string> $refFileCollection
     *
     * @return list<File>
     */
    private function normalizeFilePaths(File $openApiFile, array $refFileCollection): array
    {
        return array_map(
            static fn (string $refFile): File => new File(
                $openApiFile->getAbsolutePath() . DIRECTORY_SEPARATOR . $refFile,
            ),
            $refFileCollection,
        );
    }
}
