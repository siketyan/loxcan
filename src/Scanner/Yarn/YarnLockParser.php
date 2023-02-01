<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Yarn;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;
use Siketyan\YarnLock\YarnLock;

class YarnLockParser
{
    public function __construct(
        private YarnPackagePool $packagePool,
        private SemVerVersionParser $versionParser,
    ) {
    }

    public function parse(?string $lock): DependencyCollection
    {
        $packages = YarnLock::parse($lock ?? '');
        $dependencies = [];

        foreach ($packages as $names => $package) {
            $version = $package['version'];

            foreach (explode(',', $names) as $name) {
                $name = substr($name, 0, strrpos($name, '@', -1));
                $pkg = $this->packagePool->get($name);

                if ($pkg === null) {
                    $pkg = new Package($name);
                    $this->packagePool->add($pkg);
                }

                $dependencies[] = new Dependency(
                    $pkg,
                    $this->versionParser->parse($version),
                );
            }
        }

        return new DependencyCollection(
            $dependencies,
        );
    }
}
