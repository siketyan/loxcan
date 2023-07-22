<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Reporter;

use Siketyan\Loxcan\Exception\InvalidReporterException;

class ReporterResolver
{
    /**
     * @param iterable<ReporterInterface> $reporters
     */
    public function __construct(
        private readonly iterable $reporters,
    ) {
    }

    public function resolve(string $name): ReporterInterface
    {
        foreach ($this->reporters as $reporter) {
            if ($reporter->name() === $name) {
                return $reporter;
            }
        }

        throw new InvalidReporterException($name);
    }

    /**
     * @param iterable<string> $names
     *
     * @return \Generator<ReporterInterface>
     */
    public function resolveAll(iterable $names): \Generator
    {
        foreach ($names as $name) {
            yield $this->resolve($name);
        }
    }
}
