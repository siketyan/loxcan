<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Yarn;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTestCase;

class YarnPackagePoolTest extends AbstractPackagePoolTestCase
{
    #[Pure]
    protected function getImpl(): AbstractPackagePool
    {
        return new YarnPackagePool();
    }
}
