<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Simple;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Versioning\VersionInterface;

class SimpleVersion implements VersionInterface, \Stringable
{
    public function __construct(
        private readonly int $major,
        private readonly int $minor,
        private readonly ?int $patch,
        private readonly ?int $revision,
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

    public function getPatch(): ?int
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
            'v%d.%d',
            $this->getMajor(),
            $this->getMinor(),
        );

        if (($patch = $this->getPatch()) !== null) {
            $version .= sprintf('.%d', $patch);
        }

        if (($revision = $this->getRevision()) !== null) {
            $version .= sprintf('.%d', $revision);
        }

        return $version;
    }
}
