<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning\Composer;

use Siketyan\Loxcan\Versioning\VersionInterface;

class ComposerVersion implements VersionInterface
{
    public const STABILITIES = [
        'dev' => self::STABILITY_DEV,
        'alpha' => self::STABILITY_ALPHA,
        'beta' => self::STABILITY_BETA,
        'RC' => self::STABILITY_RC,
        'stable' => self::STABILITY_STABLE,
    ];

    public const STABILITY_DEV = 0;
    public const STABILITY_ALPHA = 10;
    public const STABILITY_BETA = 20;
    public const STABILITY_RC = 30;
    public const STABILITY_STABLE = 100;
    
    private int $x;
    private int $y;
    private int $z;
    private int $stability;
    private int $number;
    private string $hash;
    private ?string $branch;

    public function __construct(
        int $x,
        int $y,
        int $z,
        int $stability = self::STABILITY_STABLE,
        int $number = 0,
        string $hash = "",
        ?string $branch = null
    ) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->stability = $stability;
        $this->number = $number;
        $this->hash = $hash;
        $this->branch = $branch;
    }

    public function __toString(): string
    {
        if ($this->branch) {
            return sprintf(
                '%s@%s',
                $this->branch,
                $this->hash,
            );
        }

        if ($this->stability < self::STABILITY_STABLE) {
            return sprintf(
                'v%d.%d.%d-%s%d',
                $this->x,
                $this->y,
                $this->z,
                array_flip(self::STABILITIES)[$this->stability],
                $this->number,
            );
        }

        return sprintf(
            'v%d.%d.%d',
            $this->x,
            $this->y,
            $this->z,
        );
    }

    public function getX(): int
    {
        return $this->x;
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function getZ(): int
    {
        return $this->z;
    }

    public function getStability(): int
    {
        return $this->stability;
    }

    public function getNumber(): int
    {
        return $this->number;
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
