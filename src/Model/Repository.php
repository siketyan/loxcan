<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Model;

use Eloquent\Pathogen\PathInterface;

class Repository
{
    public function __construct(
        private readonly PathInterface $path,
    ) {
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }
}
