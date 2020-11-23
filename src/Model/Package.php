<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class Package
{
    private string $name;

    public function __construct(
        string $name
    ) {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
