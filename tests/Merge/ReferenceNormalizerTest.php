<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Tests\Merge;

use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\FileHandling\SpecificationFile;
use Mthole\OpenApiMerge\Merge\ReferenceNormalizer;
use Mthole\OpenApiMerge\Merge\ReferenceResolverResult;
use Mthole\OpenApiMerge\Reader\FileReader;
use Mthole\OpenApiMerge\Reader\OpenApiReaderWrapper;
use openapiphp\openapi\Writer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ReferenceNormalizer::class)]
#[CoversClass(ReferenceResolverResult::class)]
#[UsesClass(FileReader::class)]
#[UsesClass(File::class)]
#[UsesClass(SpecificationFile::class)]
#[UsesClass(OpenApiReaderWrapper::class)]
class ReferenceNormalizerTest extends TestCase
{
    public function testReadFileWithResolvedReference(): void
    {
        $file = new File(__DIR__ . '/Fixtures/openapi-with-reference.json');
        $fileReader = new FileReader();
        $openApi = $fileReader->readFile($file, false)->getOpenApi();

        $sut = new ReferenceNormalizer();

        $specificationResult = $sut->normalizeInlineReferences(
            $file,
            $openApi,
        );

        $this->assertStringEqualsFile(__DIR__ . '/Fixtures/expected/openapi-normalized.json', Writer::writeToJson($specificationResult->getNormalizedDefinition()));

        $foundRefFiles = $specificationResult->getFoundReferenceFiles();
        $this->assertCount(4, $foundRefFiles);
        $this->assertSame(__DIR__ . '/Fixtures/responseModel.json', $foundRefFiles[0]->getAbsoluteFile());
        $this->assertSame(__DIR__ . '/Fixtures/referenceModel.json', $foundRefFiles[1]->getAbsoluteFile());
        $this->assertSame(__DIR__ . '/Fixtures/sub/examples/referenceModel.json', $foundRefFiles[2]->getAbsoluteFile());
        $this->assertSame(__DIR__ . '/Fixtures/sub/examples/subType.json', $foundRefFiles[3]->getAbsoluteFile());
    }
}
