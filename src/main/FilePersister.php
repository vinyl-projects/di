<?php

declare(strict_types=1);

namespace vinyl\di;

use function chmod;
use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;
use const LOCK_EX;

/**
 * Class FilePersister
 */
class FilePersister
{
    private int $directoryMode;
    private int $fileMode;

    /**
     * FilePersister constructor.
     */
    public function __construct(?int $directoryMode = null, ?int $fileMode = null)
    {
        $this->directoryMode = $directoryMode ?? 0755;
        $this->fileMode = $fileMode ?? 0664;
    }

    /**
     * Persists file to the filesystem
     *
     * @throws \vinyl\di\FilePersisterException
     */
    public function persist(string $file, string $content): void
    {
        $destinationDirectory = dirname($file);
        if (!@is_dir($destinationDirectory)
            && !@mkdir($destinationDirectory, $this->directoryMode, true)
            && !@is_dir($destinationDirectory)) {
            throw new FilePersisterException("Directory <{$destinationDirectory}> was not created.");
        }

        $result = @file_put_contents($file, $content, LOCK_EX);

        if ($result === false) {
            throw new FilePersisterException("Something went wrong while saving <{$file}> file.");
        }

        if (@chmod($file, $this->fileMode)) {
            return;
        }

        throw new FilePersisterException(
            "Something went wrong while changing permissions for <{$file}> file."
        );
    }
}
