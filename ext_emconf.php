<?php

$EM_CONF['t3_datatable'] = [
    'title' => 'T3 DataTable',
    'description' => 'Create powerful TYPO3 backend data grids with automatic search, sorting and pagination — no custom AJAX or SQL boilerplate required.',
    'category' => 'be',
    'author' => 'HRR',
    'state' => 'beta',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.0-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.99.99',
            'php' => '8.2.0-8.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
