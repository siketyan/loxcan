<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;

class InvalidPathException extends RuntimeException
{
    #[Pure]
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
