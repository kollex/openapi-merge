<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Reader\Exception;

/**
 * @see \Mthole\OpenApiMerge\Tests\Reader\Exception\InvalidFileTypeExceptionTest
 */
class InvalidFileTypeException extends \Exception
{
    private string $fileExtension;

    public static function createFromExtension(string $fileExtension): self
    {
        $exception = new self(
            sprintf('Given file has an unsupported file extension "%s"', $fileExtension),
        );
        $exception->fileExtension = $fileExtension;

        return $exception;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }
}
