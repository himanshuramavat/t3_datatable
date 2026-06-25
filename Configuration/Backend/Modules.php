<?php

declare(strict_types=1);

use HRR\T3Datatable\Controller\Backend\DemoController;

return [
    't3datatable' => [
        'labels' => 'LLL:EXT:t3_datatable/Resources/Private/Language/locallang_mod.xlf',
        'iconIdentifier' => 't3datatable-module-icon',
        'position' => ['after' => 'file'],
        'access' => '*',
    ],
    't3datatable_demo' => [
        'parent' => 't3datatable',
        'access' => '*',
        'path' => '/module/t3datatable/demo',
        'iconIdentifier' => 't3datatable-module-icon',
        'labels' => 'LLL:EXT:t3_datatable/Resources/Private/Language/locallang_mod_demo.xlf',
        'extensionName' => 'T3Datatable',
        'routes' => [
            '_default' => [
                'target' => DemoController::class . '::indexAction',
            ],
        ],
    ],
];
