<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pub;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;
use Symfony\Component\Yaml\Yaml;

class PubLockParser
{
    public function __construct(
        private PubPackagePool $packagePool,
        private SemVerVersionParser $versionParser
    ) {
    }

    public function parse(?string $yaml): DependencyCollection
    {
        if ($yaml === null) {
            $yaml = '';
        }

        $assoc = Yaml::parse($yaml);
        $packages = $assoc['packages'] ?? [];
        $dependencies = [];

        foreach ($packages as $name => $package) {
            $version = $package['version'];
            $package = $this->packagePool->get($name);

            if ($package === null) {
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
