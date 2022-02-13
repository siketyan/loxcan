<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Npm;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class NpmPackagePoolTest extends AbstractPackagePoolTest
{
    #[Pure]
    protected function getImpl(): AbstractPackagePool
    {
        return new NpmPackagePool();
    }
}
