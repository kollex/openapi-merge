<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Util;

/**
 * @see \Mthole\OpenApiMerge\Tests\Util\JsonTest
 */
class Json
{
    /** @return array<mixed> */
    public static function toArray(mixed $data): array
    {
        return (array)json_decode(json_encode($data, \JSON_THROW_ON_ERROR) ?: '[]', true, 512, JSON_THROW_ON_ERROR);
    }
}
