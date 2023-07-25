<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\FileHandling;

use Mthole\OpenApiMerge\FileHandling\Exception\IOException;

final class File
{
    public function __construct(private string $filename)
    {
    }

    public function getFileExtension(): string
    {
        return pathinfo($this->filename, \PATHINFO_EXTENSION);
    }

    public function getAbsolutePath(): string
    {
        $fullFilename = realpath($this->filename);
        if (false === $fullFilename) {
            throw IOException::createWithNonExistingFile($this->createAbsoluteFilePath($this->filename));
        }

        return $fullFilename;
    }

    private function createAbsoluteFilePath(string $filename): string
    {
        if (0 === strpos($filename, '/')) {
            return $filename;
        }

        return getcwd() . \DIRECTORY_SEPARATOR . $filename;
    }
}
