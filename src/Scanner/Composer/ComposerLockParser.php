<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Composer;

use JsonException;
use Siketyan\Loxcan\Exception\ParseErrorException;
use Siketyan\Loxcan\Model\Dependency;
use Siketyan\Loxcan\Model\DependencyCollection;
use Siketyan\Loxcan\Model\Package;
use Siketyan\Loxcan\Versioning\Composer\ComposerVersionParser;

class ComposerLockParser
{
    public function __construct(
        private ComposerPackagePool $packagePool,
        private ComposerVersionParser $versionParser
    ) {
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

        $packages = array_merge(
            $assoc['packages'],
            $assoc['packages-dev'],
        );

        $dependencies = [];

        foreach ($packages as $package) {
            $name = $package['name'];
            $version = $package['version'];
            $hash = $package['dist']['reference'] ?? '';
            $package = $this->packagePool->get($name);

            if ($package === null) {
                $package = new Package($name);
                $this->packagePool->add($package);
            }

            $dependencies[] = new Dependency(
                $package,
                $this->versionParser->parse($version, $hash),
            );
        }

        return new DependencyCollection(
            $dependencies,
        );
    }
}
