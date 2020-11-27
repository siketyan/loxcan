<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Npm;

use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class NpmPackagePoolTest extends AbstractPackagePoolTest
{
    protected function getImpl(): AbstractPackagePool
    {
        return new NpmPackagePool();
    }
}
