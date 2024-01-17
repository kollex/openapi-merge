<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Tests\FileHandling;

use cebe\openapi\spec\OpenApi;
use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\FileHandling\SpecificationFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SpecificationFile::class)]
#[UsesClass(File::class)]
class SpecificationFileTest extends TestCase
{
    public function testGetter(): void
    {
        $stubSpecObject = $this->createStub(OpenApi::class);
        $fileStub = new File('example.file');
        $sut = new SpecificationFile($fileStub, $stubSpecObject);

        self::assertSame($fileStub, $sut->getFile());
        self::assertSame($stubSpecObject, $sut->getOpenApi());
    }
}
