<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Yarn;

use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class YarnPackagePoolTest extends AbstractPackagePoolTest
{
    protected function getImpl(): AbstractPackagePool
    {
        return new YarnPackagePool();
    }
}
