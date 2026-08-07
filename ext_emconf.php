<?php

$EM_CONF['t3_datatable'] = [
    'title' => 'T3 DataTable',
    'description' => 'T3 DataTable — server-side searchable, sortable, paginated DataTable grids for TYPO3 backend modules. No custom AJAX or SQL boilerplate required.',
    'category' => 'be',
    'author' => 'Himanshu Ramavat | Rohan Parmar',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.99.99',
            'php' => '8.2.0-8.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
