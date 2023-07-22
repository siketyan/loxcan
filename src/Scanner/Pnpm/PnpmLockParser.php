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
         *     dependencies?: array<string, array{specifier?: string, specification?: string, version: string}|string>,
         *     devDependencies?: array<string, array{specifier?: string, specification?: string, version: string}|string>,
         * } $assoc
         */
        $assoc = Yaml::parse($yaml) ?? [];
        $packages = array_merge($assoc['dependencies'] ?? [], $assoc['devDependencies'] ?? []);
        $dependencies = [];

        foreach ($packages as $name => $version) {
            $constraint = null;
            if (\is_array($version)) {
                $constraint = $version['specifier'] ?? $version['specification'] ?? null;
                $version = $version['version'];
            }

            $package = $this->packagePool->get($name, $constraint);

            if (!$package instanceof Package) {
                $package = new Package($name, $constraint);
                $this->packagePool->add($package);
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
