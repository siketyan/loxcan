<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

class Repository
{
    private string $path;

    public function __construct(
        string $path
    ) {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
