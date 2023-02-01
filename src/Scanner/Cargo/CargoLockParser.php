<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Cargo;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;
use Yosymfony\Toml\Toml;

class CargoLockParser
{
    public function __construct(
        private readonly CargoPackagePool $packagePool,
        private readonly SemVerVersionParser $versionParser,
    ) {
    }

    public function parse(?string $toml): DependencyCollection
    {
        if ($toml === null) {
            $toml = '[package]';
        }

        $assoc = Toml::parse($toml);
        $dependencies = [];

        foreach ($assoc['package'] as $package) {
            $name = $package['name'];
            $version = $package['version'];
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
