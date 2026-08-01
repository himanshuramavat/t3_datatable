<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Contract;

use HRR\T3Datatable\DataTable\GridDefinition;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Register a server-side DataTable grid for a TYPO3 database table.
 *
 * Tag implementing services with `t3datatable.grid` in your extension's Services.yaml.
 *
 * @api
 */
interface GridInterface
{
    /**
     * Unique identifier used as `?grid=` in AJAX requests.
     */
    public function getIdentifier(): string;

    /**
     * Doctrine DBAL table name, e.g. `tx_myext_records` or `be_users`.
     */
    public function getTableName(): string;

    /**
     * Decide whether this backend user may read this grid.
     *
     * Implementations must enforce the module, table, and record-level
     * permissions appropriate to the data they expose.
     */
    public function isAccessible(BackendUserAuthentication $backendUser): bool;

    /**
     * Configure columns, defaults, and query restrictions.
     */
    public function build(GridDefinition $definition): void;
}
