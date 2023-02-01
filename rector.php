<?php

declare(strict_types=1);

use Quartetcom\StaticAnalysisKit\Rector\Config;
use Rector\CodeQuality\Rector\For_\ForRepeatedCountToOwnVariableRector;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;

return static function (RectorConfig $rectorConfig): void {
    Config::use($rectorConfig);

    $rectorConfig->paths(array_map(fn (string $path) => __DIR__ . $path, [
        '/src',
        '/tests',
    ]));

    $rectorConfig->skip([
        FinalizePublicClassConstantRector::class,
        ForRepeatedCountToOwnVariableRector::class,
    ]);
};
