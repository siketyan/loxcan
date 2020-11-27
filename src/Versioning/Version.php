<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

class Version
{
    private int $major;
    private int $minor;
    private int $patch;
    private ?int $revision;

    public function __construct(
        int $major,
        int $minor,
        int $patch,
        ?int $revision
    ) {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
        $this->revision = $revision;
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
