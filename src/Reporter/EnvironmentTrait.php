<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter;

use Siketyan\Loxcan\Exception\InvalidConfigurationException;

trait EnvironmentTrait
{
    private function getEnv(string $key): string
    {
        $env = getenv($key);

        if (!\is_string($env) || $env === '') {
            throw new InvalidConfigurationException(
                sprintf(
                    'The environment variable "%s" is needed, but it is not set.',
                    $key,
                ),
            );
        }

        return $env;
    }
}
