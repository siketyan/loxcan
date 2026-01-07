<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Go;

use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;

class GoSumParser
{
    public function __construct(
        private readonly GoPackagePool $packagePool,
        private readonly SemVerVersionParser $versionParser,
    ) {
    }

    public function parse(?string $content): DependencyCollection
    {
        if ($content === null || trim($content) === '') {
            return new DependencyCollection([]);
        }

        $dependencies = [];
        $seen = [];

        foreach (explode("\n", $content) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Format: module/path v1.2.3 h1:hash=
            // or: module/path v1.2.3/go.mod h1:hash=
            $parts = preg_split('/\s+/', $line);
            if ($parts === false || count($parts) < 2) {
                continue;
            }

            $name = $parts[0];
            $version = $parts[1];

            // Remove /go.mod suffix from version
            if (str_ends_with($version, '/go.mod')) {
                $version = substr($version, 0, -7);
            }

            // Skip duplicates (same module can appear twice: once for source, once for go.mod)
            $key = $name . '@' . $version;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $package = $this->packagePool->get($name);

            if (!$package instanceof Package) {
                $package = new Package($name);
                $this->packagePool->add($package);
            }

            $dependencies[] = new Dependency(
                $package,
                $this->versionParser->parse(ltrim($version, 'v')),
            );
        }

        return new DependencyCollection($dependencies);
    }
}
