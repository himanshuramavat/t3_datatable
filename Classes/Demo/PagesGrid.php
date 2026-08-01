<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Demo;

use HRR\T3Datatable\Contract\GridInterface;
use HRR\T3Datatable\DataTable\GridDefinition;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Demo grid listing TYPO3 pages.
 */
final class PagesGrid implements GridInterface
{
    public function getIdentifier(): string
    {
        return 'demo_pages';
    }

    public function getTableName(): string
    {
        return 'pages';
    }

    public function isAccessible(BackendUserAuthentication $backendUser): bool
    {
        return $backendUser->check('modules', 't3datatable_demo');
    }

    public function build(GridDefinition $definition): void
    {
        $definition
            ->addColumn('uid', 'UID', searchable: false, orderable: true)
            ->addColumn('title', 'Title', searchable: true, orderable: true)
            ->addColumn('slug', 'Slug', searchable: true, orderable: true)
            ->addColumn('doktype', 'Doktype', searchable: false, orderable: true)
            ->addColumn('hidden', 'Hidden', searchable: false, orderable: true)
            ->setDefaultOrder('uid', 'ASC')
            ->setPageLength(10);
    }
}
