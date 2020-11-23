<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

use Eloquent\Pathogen\PathInterface;

class Repository
{
    private PathInterface $path;

    public function __construct(
        PathInterface $path
    ) {
        $this->path = $path;
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }
}
