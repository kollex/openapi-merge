<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\FileHandling;

/**
 * @see \Mthole\OpenApiMerge\Tests\FileHandling\RegexFinderTest
 */
class RegexFinder implements Finder
{
    /** @return list<string> */
    public function find(string $baseDirectory, string $searchString): array
    {
        $directoryIterator = new \RecursiveDirectoryIterator(
            $baseDirectory,
            \RecursiveDirectoryIterator::CURRENT_AS_PATHNAME | \RecursiveDirectoryIterator::SKIP_DOTS | \FilesystemIterator::SKIP_DOTS,
        );

        $regexIterator = new \RecursiveCallbackFilterIterator(
            $directoryIterator,
            static function (
                string $current,
                string $key,
                \RecursiveIterator $iterator,
            ) use (
                $baseDirectory,
                $searchString,
            ): bool {
                if ($iterator->hasChildren()) {
                    return true;
                }

                $relativeFileName = '.' . substr($current, \strlen($baseDirectory));

                return 1 === preg_match(
                    sprintf('~%s~i', str_replace('~', '\~', $searchString)),
                    $relativeFileName,
                );
            },
        );

        $recursiveIterator = new \RecursiveIteratorIterator($regexIterator);

        $matches = array_values(iterator_to_array($recursiveIterator));

        \assert(
            array_filter($matches, static fn (mixed $input): bool => \is_string($input)) === $matches,
        );

        return $matches;
    }
}
