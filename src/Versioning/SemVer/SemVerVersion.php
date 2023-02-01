<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\SemVer;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Versioning\CompatibilityAwareInterface;
use Siketyan\Loxcan\Versioning\HasSemVerLikeCompatibility;
use Siketyan\Loxcan\Versioning\VersionInterface;

class SemVerVersion implements VersionInterface, CompatibilityAwareInterface, \Stringable
{
    use HasSemVerLikeCompatibility;

    /**
     * @param string[] $preRelease
     * @param string[] $build
     */
    public function __construct(
        private readonly int $major,
        private readonly int $minor,
        private readonly int $patch,
        private readonly array $preRelease = [],
        private readonly array $build = [],
    ) {
    }

    public function __toString(): string
    {
        $version = sprintf(
            '%d.%d.%d',
            $this->major,
            $this->minor,
            $this->patch,
        );

        if ($this->preRelease !== []) {
            $version .= sprintf(
                '-%s',
                implode('.', $this->preRelease),
            );
        }

        if ($this->build !== []) {
            $version .= sprintf(
                '+%s',
                implode('.', $this->build),
            );
        }

        return $version;
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

    /**
     * @return string[]
     */
    public function getPreRelease(): array
    {
        return $this->preRelease;
    }

    /**
     * @return string[]
     */
    public function getBuild(): array
    {
        return $this->build;
    }

    public function isPreRelease(): bool
    {
        return $this->preRelease !== [];
    }

    // region Aliases for HasSemVerLikeCompatibility trait
    #[Pure]
    protected function getX(): int
    {
        return $this->getMajor();
    }

    #[Pure]
    protected function getY(): int
    {
        return $this->getMinor();
    }
    // endregion
}
