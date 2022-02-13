<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pip;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class PipPackagePoolTest extends AbstractPackagePoolTest
{
    #[Pure]
    protected function getImpl(): AbstractPackagePool
    {
        return new PipPackagePool();
    }
}
