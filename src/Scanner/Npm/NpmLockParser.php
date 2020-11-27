<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Npm;

use JsonException;
use Siketyan\Loxcan\Exception\ParseErrorException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\SemVer\SemVerVersionParser;

class NpmLockParser
{
    private NpmPackagePool $packagePool;
    private SemVerVersionParser $versionParser;

    public function __construct(
        NpmPackagePool $packagePool,
        SemVerVersionParser $versionParser
    ) {
        $this->packagePool = $packagePool;
        $this->versionParser = $versionParser;
    }

    public function parse(?string $json): DependencyCollection
    {
        if ($json === null) {
            $json = '{}';
        }

        try {
            $assoc = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ParseErrorException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious(),
            );
        }

        $packages = $assoc['packages'] ?? [];
        $dependencies = [];

        foreach ($packages as $name => $package) {
            $name = preg_replace('/^node_modules\//', '', $name);
            $version = $package['version'];
            $package = $this->packagePool->get($name);

            if ($name === '') {
                continue;
            }

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
