<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\FileHandling;

use Mthole\OpenApiMerge\FileHandling\Exception\IOException;

/**
 * @see \Mthole\OpenApiMerge\Tests\FileHandling\FileTest
 */
final readonly class File
{
    public function __construct(private string $filename)
    {
    }

    public function getFileExtension(): string
    {
        return pathinfo($this->filename, \PATHINFO_EXTENSION);
    }

    public function getAbsoluteFile(): string
    {
        $fullFilename = realpath($this->filename);
        if (false === $fullFilename) {
            throw IOException::createWithNonExistingFile($this->createAbsoluteFilePath($this->filename));
        }

        return $fullFilename;
    }

    public function getAbsolutePath(): string
    {
        return \dirname($this->getAbsoluteFile());
    }

    private function createAbsoluteFilePath(string $filename): string
    {
        if (str_starts_with($filename, '/')) {
            return $filename;
        }

        return getcwd() . \DIRECTORY_SEPARATOR . $filename;
    }
}
