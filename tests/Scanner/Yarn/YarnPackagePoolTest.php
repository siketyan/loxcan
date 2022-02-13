<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Yarn;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class YarnPackagePoolTest extends AbstractPackagePoolTest
{
    #[Pure]
    protected function getImpl(): AbstractPackagePool
    {
        return new YarnPackagePool();
    }
}
