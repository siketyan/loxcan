<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Composer;

use Siketyan\Loxcan\Exception\ParseErrorException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Composer\ComposerVersionParser;

class ComposerLockParser
{
    public function __construct(
        private readonly ComposerPackagePool $packagePool,
        private readonly ComposerVersionParser $versionParser,
    ) {
    }

    public function parse(?string $json): DependencyCollection
    {
        if ($json === null) {
            $json = '{}';
        }

        try {
            /**
             * @var array{
             *     packages: list<array{name: string, version: string, dist: array{reference?: string}}>,
             *     packages-dev: list<array{name: string, version: string, dist: array{reference?: string}}>,
             * } $assoc
             */
            $assoc = json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ParseErrorException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious(),
            );
        }

        $packages = [
            ...$assoc['packages'],
            ...$assoc['packages-dev'],
        ];

        $dependencies = [];

        foreach ($packages as $package) {
            $name = $package['name'];
            $version = $package['version'];
            $hash = $package['dist']['reference'] ?? '';
            $package = $this->packagePool->get($name);

            if (!$package instanceof Package) {
                $package = new Package($name);
                $this->packagePool->add($package);
            }

            $dependencies[] = new Dependency(
                $package,
                $this->versionParser->parse($version, $hash),
            );
        }

        return new DependencyCollection(
            $dependencies,
        );
    }
}
