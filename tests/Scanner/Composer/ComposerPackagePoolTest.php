<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Composer;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTest;

class ComposerPackagePoolTest extends AbstractPackagePoolTest
{
    #[Pure]
    protected function getImpl(): AbstractPackagePool
    {
        return new ComposerPackagePool();
    }
}
