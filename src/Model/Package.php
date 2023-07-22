<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class Package
{
    public function __construct(
        private readonly string $name,
        private readonly ?string $constraint = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getConstraint(): ?string
    {
        return $this->constraint;
    }
}
