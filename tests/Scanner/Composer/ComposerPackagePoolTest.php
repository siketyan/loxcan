<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Composer;

use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class ComposerPackagePoolTest extends AbstractPackagePoolTest
{
    protected function getImpl(): AbstractPackagePool
    {
        return new ComposerPackagePool();
    }
}
