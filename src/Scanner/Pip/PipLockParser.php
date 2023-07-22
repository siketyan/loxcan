<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pip;

use Siketyan\Loxcan\Exception\ParseErrorException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersionParser;

class PipLockParser
{
    public function __construct(
        private readonly PipPackagePool $packagePool,
        private readonly SimpleVersionParser $versionParser,
    ) {
    }

    public function parse(?string $json): DependencyCollection
    {
        if ($json === null) {
            $json = '{}';
        }

        try {
            /** @var array{default?: array<string, array{}>, develop?: array<string, array{}>} $assoc */
            $assoc = json_decode($json, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ParseErrorException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious(),
            );
        }

        /** @var array<string, array{version: string}> $packages */
        $packages = array_merge($assoc['default'] ?? [], $assoc['develop'] ?? []);
        $dependencies = [];

        foreach ($packages as $name => $package) {
            $version = $package['version'];
            if (!str_starts_with($version, '==')) {
                continue;
            }

            $version = substr($version, 2);
            $package = $this->packagePool->get($name);

            if (!$package instanceof Package) {
                $package = new Package($name);
                $this->packagePool->add($package);
            }

            $dependencies[] = new Dependency(
                $package,
                $this->versionParser->parse($version),
            );
        }

        return new DependencyCollection(
            $dependencies,
        );
    }
}
