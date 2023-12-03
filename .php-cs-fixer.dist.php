<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use Quartetcom\StaticAnalysisKit\PhpCsFixer\Config;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->exclude('vendor')
;

return (new Config())
    ->addRiskyRules([
        'no_trailing_whitespace_in_string' => false,
        'php_unit_data_provider_return_type' => false,
        'static_lambda' => false,
    ])
    ->setFinder($finder)
;
