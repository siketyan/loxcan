<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Pub;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTestCase;

class PubPackagePoolTest extends AbstractPackagePoolTestCase
{
    #[Pure]
    protected function getImpl(): AbstractPackagePool
    {
        return new PubPackagePool();
    }
}
