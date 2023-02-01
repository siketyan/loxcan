<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class Package
{
    public function __construct(
        private string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
