<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Quartetcom\StaticAnalysisKit\PhpCsFixer\Config;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
;

return (new Config())
    ->setFinder($finder)
;
