<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pnpm;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;
use Symfony\Component\Yaml\Yaml;

class PnpmLockParser
{
    public function __construct(
        private readonly PnpmPackagePool $packagePool,
        private readonly SemVerVersionParser $versionParser,
    ) {
    }

    public function parse(?string $yaml): DependencyCollection
    {
        if ($yaml === null) {
            $yaml = '';
        }

        /**
         * @var array{
         *     dependencies?: array<string, array{version: string}|string>,
         *     devDependencies?: array<string, array{version: string}|string>,
         * } $assoc
         */
        $assoc = Yaml::parse($yaml) ?? [];
        $packages = array_merge($assoc['dependencies'] ?? [], $assoc['devDependencies'] ?? []);
        $dependencies = [];

        foreach ($packages as $name => $version) {
            $package = $this->packagePool->get($name);

            if (!$package instanceof Package) {
                $package = new Package($name);
                $this->packagePool->add($package);
            }

            if (\is_array($version)) {
                $version = $version['version'];
            }

            /** @var string $version */
            $version = preg_replace('/\(.+\)/', '', $version);

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
