<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Tests\Writer\Exception;

use Mthole\OpenApiMerge\Writer\Exception\InvalidFileTypeException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InvalidFileTypeException::class)]
class InvalidFileTypeExceptionTest extends TestCase
{
    public function testCreateException(): void
    {
        $exception = InvalidFileTypeException::createFromExtension('exe');
        $this->assertSame('exe', $exception->getFileExtension());
        $this->assertSame('The filetype "exe" is not supported for dumping', $exception->getMessage());
    }
}
