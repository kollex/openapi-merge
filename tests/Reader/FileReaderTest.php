<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Tests\Reader;

use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\Reader\Exception\InvalidFileTypeException;
use Mthole\OpenApiMerge\Reader\FileReader;
use PHPUnit\Framework\TestCase;

/**
 * @uses \Mthole\OpenApiMerge\FileHandling\File
 * @uses \Mthole\OpenApiMerge\FileHandling\SpecificationFile
 * @uses \Mthole\OpenApiMerge\Reader\Exception\InvalidFileTypeException
 *
 * @covers \Mthole\OpenApiMerge\Reader\FileReader
 */
class FileReaderTest extends TestCase
{
    /** @dataProvider validFilesDataProvider */
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

        self::expectException(InvalidFileTypeException::class);
        $sut->readFile($file);
    }
}
