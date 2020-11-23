<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Exception;

use Throwable;

class UnsupportedVersionException extends RuntimeException
{
    public function __construct(string $version, $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'Unsupported version "%s" was provided.',
                $version,
            ),
            $code,
            $previous,
        );
    }
}
