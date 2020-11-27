<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pub;

use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class PubPackagePoolTest extends AbstractPackagePoolTest
{
    protected function getImpl(): AbstractPackagePool
    {
        return new PubPackagePool();
    }
}
