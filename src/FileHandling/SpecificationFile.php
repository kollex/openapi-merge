<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\FileHandling;

use openapiphp\openapi\spec\OpenApi;

/**
 * @see \Mthole\OpenApiMerge\Tests\FileHandling\SpecificationFileTest
 */
class SpecificationFile
{
    public function __construct(private readonly File $file, private readonly OpenApi $openApi)
    {
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getOpenApi(): OpenApi
    {
        return $this->openApi;
    }
}
