<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Tests\Reader;

use cebe\openapi\spec\OpenApi;
use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\FileHandling\SpecificationFile;
use Mthole\OpenApiMerge\Reader\Exception\InvalidFileTypeException;
use Mthole\OpenApiMerge\Reader\FileReader;
use Mthole\OpenApiMerge\Reader\OpenApiReaderWrapper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileReader::class)]
#[UsesClass(File::class)]
#[UsesClass(SpecificationFile::class)]
#[UsesClass(InvalidFileTypeException::class)]
#[UsesClass(OpenApiReaderWrapper::class)]
final class FileReaderTest extends TestCase
{
    private const DUMMY_JSON_FILE = __DIR__ . '/Fixtures/valid-openapi.json';
    private const DUMMY_YAML_FILE = __DIR__ . '/Fixtures/valid-openapi.yml';

    #[DataProvider('validFilesDataProvider')]
    public function testValidFiles(string $filename): void
    {
        $file = new File($filename);
        $sut = new FileReader();
        $specification = $sut->readFile($file);

        self::assertSame($file, $specification->getFile());
    }

    /** @return \Generator<string[]> */
    public static function validFilesDataProvider(): \Generator
    {
        yield [__DIR__ . '/Fixtures/valid-openapi.yml'];
        yield [__DIR__ . '/Fixtures/valid-openapi.yaml'];
        yield [__DIR__ . '/Fixtures/valid-openapi.json'];
    }

    public function testInvalidFile(): void
    {
        $sut = new FileReader();
        $file = new File('openapi.neon');

        $this->expectException(InvalidFileTypeException::class);
        $sut->readFile($file);
    }

    #[DataProvider('passResolveReferenceJsonProvider')]
    public function testPassResolveReferenceForJsonFile(string $providedFileName, bool $providedResolveReferences): void
    {
        $readerMock = $this->createStub(OpenApiReaderWrapper::class);

        $readerMock->method('readFromJsonFile')
            ->willReturn(new OpenApi([]));

        $sut = new FileReader($readerMock);

        $result = $sut->readFile(new File($providedFileName), $providedResolveReferences);
        $this->assertInstanceOf(SpecificationFile::class, $result);
        $this->assertSame($providedFileName, $result->getFile()->getAbsoluteFile());
    }

    #[DataProvider('passResolveReferenceYamlProvider')]
    public function testPassResolveReferenceForYamlFile(string $providedFileName, bool $providedResolveReferences): void
    {
        $readerMock = $this->createStub(OpenApiReaderWrapper::class);

        $readerMock->method('readFromYamlFile')
            ->willReturn(new OpenApi([]));

        $sut = new FileReader($readerMock);

        $result = $sut->readFile(new File($providedFileName), $providedResolveReferences);
        $this->assertInstanceOf(SpecificationFile::class, $result);
        $this->assertSame($providedFileName, $result->getFile()->getAbsoluteFile());
    }

    public static function passResolveReferenceJsonProvider(): \Generator
    {
        yield [self::DUMMY_JSON_FILE,  true];
        yield [self::DUMMY_JSON_FILE,  false];
    }

    public static function passResolveReferenceYamlProvider(): \Generator
    {
        yield [self::DUMMY_YAML_FILE, true];
        yield [self::DUMMY_YAML_FILE, false];
    }
}
