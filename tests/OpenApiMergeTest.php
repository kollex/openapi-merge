<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Tests;

use cebe\openapi\exceptions\TypeErrorException;
use cebe\openapi\spec\OpenApi;
use cebe\openapi\spec\Paths;
use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\FileHandling\SpecificationFile;
use Mthole\OpenApiMerge\Merge\ComponentsMerger;
use Mthole\OpenApiMerge\Merge\PathMerger;
use Mthole\OpenApiMerge\Merge\ReferenceNormalizer;
use Mthole\OpenApiMerge\Merge\ReferenceResolverResult;
use Mthole\OpenApiMerge\OpenApiMerge;
use Mthole\OpenApiMerge\Reader\Exception\InvalidFileTypeException;
use Mthole\OpenApiMerge\Reader\FileReader;
use Mthole\OpenApiMerge\Reader\OpenApiReaderWrapper;
use Mthole\OpenApiMerge\Util\Json;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OpenApiMerge::class)]
#[UsesClass(File::class)]
#[UsesClass(SpecificationFile::class)]
#[UsesClass(FileReader::class)]
#[UsesClass(PathMerger::class)]
#[UsesClass(OpenApiReaderWrapper::class)]
#[UsesClass(ReferenceResolverResult::class)]
#[UsesClass(ComponentsMerger::class)]
#[UsesClass(Json::class)]
class OpenApiMergeTest extends TestCase
{
    public function testMergePaths(): void
    {
        $sut = new OpenApiMerge(
            new FileReader(),
            [
                new PathMerger(),
                new ComponentsMerger(),
            ],
            new ReferenceNormalizer(),
        );

        $result = $sut->mergeFiles(
            new File(__DIR__ . '/Fixtures/base.yml'),
            [
                new File(__DIR__ . '/Fixtures/empty.yml'),
                new File(__DIR__ . '/Fixtures/routes.yml'),
                new File(__DIR__ . '/Fixtures/errors.yml'),
            ],
        )->getOpenApi();
        \assert($result instanceof OpenApi);

        self::assertCount(1, $result->paths->getPaths());
        self::assertNotNull($result->components);
        self::assertIsArray($result->components->schemas);
    }

    public function testMergeFileWithoutComponents(): void
    {
        $sut = new OpenApiMerge(
            new FileReader(),
            [
                new PathMerger(),
                new ComponentsMerger(),
            ],
            new ReferenceNormalizer(),
        );

        $result = $sut->mergeFiles(
            new File(__DIR__ . '/Fixtures/base-without-components.yml'),
            [],
        )->getOpenApi();
        \assert($result instanceof OpenApi);

        self::assertNull($result->components);
    }

    /**
     * @throws InvalidFileTypeException
     * @throws TypeErrorException
     */
    public function testReferenceNormalizer(): void
    {
        $referenceNormalizer = $this->createMock(ReferenceNormalizer::class);
        $referenceNormalizer->expects(
            self::exactly(2),
        )->method('normalizeInlineReferences')->willReturnCallback(
            static function (
                File $openApiFile,
                OpenApi $openApiDefinition,
            ) {
                $foundReferences = [];
                if ($openApiFile->getAbsoluteFile() === __DIR__ . '/Fixtures/errors.yml') {
                    $foundReferences[] = new File(__DIR__ . '/Fixtures/routes.yml');
                }

                return new ReferenceResolverResult(
                    $openApiDefinition,
                    $foundReferences,
                );
            }
        );

        $sut = new OpenApiMerge(
            new FileReader(),
            [
                new PathMerger(),
                new ComponentsMerger(),
            ],
            $referenceNormalizer,
        );

        $mergedResult = $sut->mergeFiles(
            new File(__DIR__ . '/Fixtures/base.yml'),
            [
                new File(__DIR__ . '/Fixtures/errors.yml'),
            ],
            false,
        );

        $mergedDefinition = $mergedResult->getOpenApi();
        if (null === $mergedDefinition->paths) {
            $mergedDefinition->paths = new Paths([]);
        }

        self::assertCount(1, $mergedDefinition->paths);
        self::assertSame(
            ['ProblemResponse', 'pingResponse'],
            array_keys($mergedDefinition->components->schemas), // @phpstan-ignore-line
        );
    }

    public function testReferenceNormalizerWillNotBeExecuted(): void
    {
        $referenceNormalizer = $this->createMock(ReferenceNormalizer::class);
        $referenceNormalizer->expects(self::never())->method('normalizeInlineReferences');

        $sut = new OpenApiMerge(
            new FileReader(),
            [],
            $referenceNormalizer,
        );

        $sut->mergeFiles(
            new File(__DIR__ . '/Fixtures/base.yml'),
            [
                new File(__DIR__ . '/Fixtures/errors.yml'),
            ],
        );
    }
}
