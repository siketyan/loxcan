<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Composer;

use Siketyan\Loxcan\Versioning\CompatibilityAwareInterface;
use Siketyan\Loxcan\Versioning\HasSemVerLikeCompatibility;
use Siketyan\Loxcan\Versioning\VersionInterface;

class ComposerVersion implements VersionInterface, CompatibilityAwareInterface, \Stringable
{
    use HasSemVerLikeCompatibility;

    public function __construct(
        private readonly string $normalized,
        private readonly string $pretty,
        private readonly int $major,
        private readonly int $minor,
        private readonly int $patch,
        private readonly string $hash = '',
        private readonly ?string $branch = null,
    ) {
    }

    public function __toString(): string
    {
        if ($this->branch !== null) {
            return sprintf(
                '%s@%s',
                $this->branch,
                $this->hash,
            );
        }

        return $this->pretty;
    }

    public function getNormalized(): string
    {
        return $this->normalized;
    }

    public function getPretty(): string
    {
        return $this->pretty;
    }

    public function getX(): int
    {
        return $this->major;
    }

    public function getY(): int
    {
        return $this->minor;
    }

    public function getZ(): int
    {
        return $this->patch;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getBranch(): ?string
    {
        return $this->branch;
    }
}
