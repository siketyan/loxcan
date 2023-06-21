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

        $assoc = Yaml::parse($yaml) ?? [];
        $packages = array_merge($assoc['dependencies'] ?? [], $assoc['devDependencies'] ?? []) ?? [];
        $dependencies = [];

        foreach ($packages as $name => $versionInfo) {
            $package = $this->packagePool->get($name);

            if (!$package instanceof Package) {
                $package = new Package($name);
                $this->packagePool->add($package);
            }

            $version = match (true) {
                \is_array($versionInfo) => $versionInfo['version'],
                default => $versionInfo,
            };
            $version = preg_replace('/\(.+\)/', '', (string) $version);

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
