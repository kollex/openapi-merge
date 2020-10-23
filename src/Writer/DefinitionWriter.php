<?php

declare(strict_types=1);

namespace OpenApiMerge\Writer;

use cebe\openapi\Writer;
use OpenApiMerge\FileHandling\SpecificationFile;
use OpenApiMerge\Writer\Exception\InvalidFileTypeException;

final class DefinitionWriter
{
    public function write(SpecificationFile $specFile): string
    {
        switch ($specFile->getFile()->getFileExtension()) {
            case 'json':
                return $this->writeToJson($specFile);

            case 'yml':
            case 'yaml':
                return $this->writeToYaml($specFile);

            default:
                throw InvalidFileTypeException::createFromExtension($specFile->getFile()->getFileExtension());
        }
    }

    public function writeToJson(SpecificationFile $specFile): string
    {
        return Writer::writeToJson($specFile->getOpenApiSpecificationObject());
    }

    public function writeToYaml(SpecificationFile $specFile): string
    {
        return Writer::writeToYaml($specFile->getOpenApiSpecificationObject());
    }
}
