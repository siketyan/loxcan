<?php

declare(strict_types=1);

use Quartetcom\StaticAnalysisKit\Rector\Config;
use Rector\CodeQuality\Rector\For_\ForRepeatedCountToOwnVariableRector;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\Class_\CoversAnnotationWithValueToAttributeRector;
use Rector\PHPUnit\AnnotationsToAttributes\Rector\ClassMethod\DataProviderAnnotationToAttributeRector;
use Rector\PHPUnit\PHPUnit100\Rector\Class_\StaticDataProviderClassMethodRector;

return static function (RectorConfig $rectorConfig): void {
    Config::use($rectorConfig);

    $rectorConfig->paths(array_map(fn (string $path) => __DIR__ . $path, [
        '/src',
        '/tests',
    ]));

    $rectorConfig->skip([
        CoversAnnotationWithValueToAttributeRector::class,
        DataProviderAnnotationToAttributeRector::class,
        FinalizePublicClassConstantRector::class,
        ForRepeatedCountToOwnVariableRector::class,
        StaticDataProviderClassMethodRector::class,
    ]);
};
