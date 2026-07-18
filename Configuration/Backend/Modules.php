<?php

declare(strict_types=1);

use HRR\T3Datatable\Controller\Backend\DemoController;
use TYPO3\CMS\Core\Information\Typo3Version;

$webModuleParent = (new Typo3Version())->getMajorVersion() >= 14 ? 'content' : 'web';

return [
    't3datatable_demo' => [
        'parent' => $webModuleParent,
        'position' => ['after' => 'web_list'],
        'access' => 'user',
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
