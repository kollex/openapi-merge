<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Writer\Exception;

/**
 * @see \Mthole\OpenApiMerge\Tests\Writer\Exception\InvalidFileTypeExceptionTest
 */
class InvalidFileTypeException extends \Exception
{
    private string $fileExtension;

    public static function createFromExtension(string $fileExtension): self
    {
        $exception = new self(
            sprintf('The filetype "%s" is not supported for dumping', $fileExtension),
        );
        $exception->fileExtension = $fileExtension;

        return $exception;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }
}
