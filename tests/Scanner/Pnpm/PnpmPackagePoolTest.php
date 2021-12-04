<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pnpm;

use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class PnpmPackagePoolTest extends AbstractPackagePoolTest
{
    protected function getImpl(): AbstractPackagePool
    {
        return new PnpmPackagePool();
    }
}
