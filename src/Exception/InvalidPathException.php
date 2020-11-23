<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Exception;

use Throwable;

class InvalidPathException extends RuntimeException
{
    public function __construct($path, $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'The path "%s" is not valid.',
                $path,
            ),
            $code,
            $previous,
        );
    }
}
