<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Versioning;

interface SemVerLikeInterface
{
    public function getX(): int;

    public function getY(): int;
}
