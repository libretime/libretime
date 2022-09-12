<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->notPath('application/configs/conf.php')
    ->exclude('application/models/airtime/map')
    ->exclude('application/models/airtime/om');


$extra_rules = [
    'phpdoc_order' => false,
];

if (version_compare(PhpCsFixer\Console\Application::VERSION, '3.11.0') >= 0) {
    $extra_rules['phpdoc_order'] = ['order' => ['param', 'return', 'throws']];
}

$config = new PhpCsFixer\Config();
return $config->setRules(array_merge(
    [
        '@PhpCsFixer' => true,
        'concat_space' => ['spacing' => 'one'],
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],
        'ordered_class_elements' => false,
        'yoda_style' => false,
    ],
    $extra_rules
))
    ->setFinder($finder);
