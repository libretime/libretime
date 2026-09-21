<?php

use Rector\Config\RectorConfig;
use Rector\Php82\Rector\Encapsed\VariableInStringInterpolationFixerRector;
use Rector\Php82\Rector\FuncCall\Utf8DecodeEncodeToMbConvertEncodingRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/application',
        __DIR__ . '/build',
        __DIR__ . '/install',
        __DIR__ . '/public',
        __DIR__ . '/tests',
    ])
    ->withSkip([
        __DIR__ . '/application/configs/conf.php',
        __DIR__ . '/application/models/airtime/map',
        __DIR__ . '/application/models/airtime/om',
        __DIR__ . '/tools/vendor',
        __DIR__ . '/vendor',
    ])
    ->withBootstrapFiles([__DIR__ . '/vendor/autoload.php'])
    ->withFileExtensions(['php', 'phtml'])
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withRules([
        VariableInStringInterpolationFixerRector::class,
        Utf8DecodeEncodeToMbConvertEncodingRector::class,
    ]);
