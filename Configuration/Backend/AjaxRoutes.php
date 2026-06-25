<?php

declare(strict_types=1);

use HRR\T3Datatable\Controller\DataTableController;

return [
    't3datatable_data' => [
        'path' => '/t3datatable/data',
        'target' => DataTableController::class . '::dataAction',
    ],
];
