<?php

declare(strict_types=1);

namespace Siketyan\Loxcan\Scanner\Composer;

use JetBrains\PhpStorm\Pure;
use Siketyan\Loxcan\Scanner\AbstractPackagePool;
use Siketyan\Loxcan\Scanner\AbstractPackagePoolTestCase;

class ComposerPackagePoolTest extends AbstractPackagePoolTestCase
{
    #[Pure]
    protected function getImpl(): AbstractPackagePool
    {
        return new ComposerPackagePool();
    }
}
