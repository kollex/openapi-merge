<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Filesystem;

final class DirReader implements DirReaderInterface
{
    /** @return string[] */
    public function getDirContents(string $dir): array
    {
        return $this->readDirContents($dir);
    }

    /**
     * @param string[] $results
     *
     * @return string[]
     */
    private function readDirContents(string $dir, array &$results = []): array
    {
        $files = scandir($dir);

        if (false === $files) {
            throw new \InvalidArgumentException(sprintf('Provided dir "%s" is not readable', $dir));
        }

        foreach ($files as $value) {
            $path = realpath($dir . \DIRECTORY_SEPARATOR . $value);

            if (false === $path) {
                throw new \InvalidArgumentException(sprintf('Path "%s" is not readable', $path));
            }

            if (!is_dir($path)) {
                $results[] = $path;
            } elseif ('.' !== $value && '..' !== $value) {
                $this->readDirContents($path, $results);
            }
        }

        return $results;
    }
}
