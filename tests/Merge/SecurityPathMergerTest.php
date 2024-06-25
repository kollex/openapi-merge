<?php

declare(strict_types=1);

namespace Merge;

use cebe\openapi\spec\OpenApi;
use Mthole\OpenApiMerge\Merge\SecurityPathMerger;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SecurityPathMerger::class)]
#[UsesClass('\Mthole\OpenApiMerge\Util\Json')]
class SecurityPathMergerTest extends TestCase
{
    #[DataProvider('mergeDataProvider')]
    public function testMerge(
        OpenApi $existingSpec,
        OpenApi $newSpec,
        OpenApi $expectedSpec,
    ): void {
        $sut = new SecurityPathMerger();

        $result = $sut->merge(
            $existingSpec,
            $newSpec,
        );

        $stateBefore = $existingSpec->getSerializableData();
        self::assertEquals($expectedSpec, $result);
        self::assertEquals($stateBefore, $existingSpec->getSerializableData());
    }

    /** @return iterable<string, list<OpenApi|null>> */
    public static function mergeDataProvider(): iterable
    {
        yield 'empty' => [
            new OpenApi([]),
            new OpenApi([]),
            new OpenApi([]),
        ];

        yield 'full' => [
            new OpenApi([
                'paths' => [
                    '/authExisting' => [
                        'get' => [
                            'responses' => [
                                '200' => ['description' => 'OK'],
                            ],
                        ],
                    ],
                    '/auth' => [
                        'get' => [
                            'responses' => [
                                '200' => ['description' => 'OK'],
                            ],
                        ],
                        'post' => [
                            'responses' => [
                                '200' => ['description' => 'OK'],
                            ],
                        ],
                    ],
                ],
            ]),
            new OpenApi([
                'paths' => [
                    '/auth' => [
                        'get' => [
                            'responses' => [
                                '200' => ['description' => 'OK'],
                            ],
                        ],
                        'post' => [
                            'security' => [],
                            'responses' => [
                                '200' => ['description' => 'OK'],
                            ],
                        ],
                    ],
                ],
                'security' => [
                    'BasicAuth' => [],
                ],
                'components' => [
                    'securitySchemes' => [
                        'BasicAuth' => ['type' => 'http'],
                    ],
                ],
            ]),
            new OpenApi([
                'paths' => [
                    '/authExisting' => [
                        'get' => [
                            'responses' => [
                                '200' => ['description' => 'OK'],
                            ],
                        ],
                    ],
                    '/auth' => [
                        'get' => [
                            'responses' => [
                                '200' => ['description' => 'OK'],
                            ],
                            'security' => [
                                'BasicAuth' => [],
                            ],
                        ],
                        'post' => [
                            'responses' => [
                                '200' => ['description' => 'OK'],
                            ],
                        ],
                    ],
                ],
            ]),
        ];
    }
}
