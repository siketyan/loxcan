<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Yarn;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;
use Siketyan\YarnLock\ConstraintInterface;
use Siketyan\YarnLock\YarnLock;

class YarnLockParser
{
    public function __construct(
        private readonly YarnPackagePool $packagePool,
        private readonly SemVerVersionParser $versionParser,
    ) {
    }

    public function parse(?string $lock): DependencyCollection
    {
        $packages = YarnLock::packages($lock ?? '');
        $dependencies = [];

        foreach ($packages as $package) {
            $version = $package->getVersion();

            /** @var ConstraintInterface $constraint */
            foreach ($package->getConstraints()->all() as $constraint) {
                $name = $constraint->getName();
                $pkg = $this->packagePool->get($name);

                if (!$pkg instanceof Package) {
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
