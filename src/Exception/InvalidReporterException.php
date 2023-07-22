<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Exception;

class InvalidReporterException extends RuntimeException
{
    public function __construct(string $name, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf('The reporter "%s" is not valid.', $name),
            $code,
            $previous,
        );
    }
}
