<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Tests\Reader\Exception;

use Mthole\OpenApiMerge\Reader\Exception\InvalidFileTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidFileTypeException::class)]
class InvalidFileTypeExceptionTest extends TestCase
{
    public function testCreateException(): void
    {
        $exception = InvalidFileTypeException::createFromExtension('exe');
        $this->assertSame('exe', $exception->getFileExtension());
        $this->assertSame('Given file has an unsupported file extension "exe"', $exception->getMessage());
    }
}
