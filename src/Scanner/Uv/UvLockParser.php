<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Uv;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Simple\SimpleVersionParser;
use Yosymfony\Toml\Toml;

class UvLockParser
{
    public function __construct(
        private readonly UvPackagePool $packagePool,
        private readonly SimpleVersionParser $versionParser,
    ) {
    }

    public function parse(?string $toml): DependencyCollection
    {
        if ($toml === null || trim($toml) === '') {
            return new DependencyCollection([]);
        }

        /** @var array{package?: list<array{name: string, version: string}>} $assoc */
        $assoc = Toml::parse($toml);
        $dependencies = [];

        foreach ($assoc['package'] ?? [] as $package) {
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

        return new DependencyCollection($dependencies);
    }
}
