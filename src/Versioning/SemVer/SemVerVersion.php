<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\SemVer;

use Siketyan\Loxcan\Versioning\VersionInterface;

class SemVerVersion implements VersionInterface
{
    private int $major;
    private int $minor;
    private int $patch;

    /**
     * @var mixed[]
     */
    private array $preRelease;

    /**
     * @var mixed[]
     */
    private array $build;

    public function __construct(
        int $major,
        int $minor,
        int $patch,
        array $preRelease = [],
        array $build = []
    ) {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->preRelease = $preRelease;
        $this->build = $build;
    }

    public function __toString(): string
    {
        $version = sprintf(
            '%d.%d.%d',
            $this->major,
            $this->minor,
            $this->patch,
        );

        if (count($this->preRelease)) {
            $version .= sprintf(
                '-%s',
                implode('.', $this->preRelease),
            );
        }

        if (count($this->build)) {
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
     * @return mixed[]
     */
    public function getPreRelease(): array
    {
        return $this->preRelease;
    }

    /**
     * @return mixed[]
     */
    public function getBuild(): array
    {
        return $this->build;
    }

    public function isPreRelease(): bool
    {
        return count($this->preRelease) > 0;
    }
}
