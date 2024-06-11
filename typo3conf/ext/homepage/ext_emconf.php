<?php

/**
 * Extension Manager/Repository config file for ext "homepage".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Homepage',
    'description' => '',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'bootstrap_package' => '13.0.0-14.9.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'UniTrier\\Homepage\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'Lars C',
    'author_email' => 's4lacarp@uni-trier.de',
    'author_company' => 'Uni Trier',
    'version' => '1.0.0',
];
