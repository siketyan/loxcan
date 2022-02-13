<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Simple;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Versioning\VersionInterface;

class SimpleVersion implements VersionInterface
{
    public function __construct(
        private int $major,
        private int $minor,
        private int $patch,
        private ?int $revision
    ) {
    }

    public function getMajor(): int
    {
        return $this->major;
    }

    public function getMinor(): int
    {
        return $this->minor;
    }

    public function getPatch(): int
    {
        return $this->patch;
    }

    public function getRevision(): ?int
    {
        return $this->revision;
    }

    #[Pure]
    public function __toString(): string
    {
        $version = sprintf(
            'v%d.%d.%d',
            $this->major,
            $this->minor,
            $this->patch,
        );

        if ($this->getRevision() !== null) {
            $version .= sprintf(
                '.%d',
                $this->revision,
            );
        }

        return $version;
    }
}
