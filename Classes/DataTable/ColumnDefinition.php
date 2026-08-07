<?php

declare(strict_types=1);

namespace HRR\T3Datatable\DataTable;

/**
 * Immutable column metadata for a grid.
 *
 * @api
 */
final readonly class ColumnDefinition
{
    public function __construct(
        public string $name,
        public string $label,
        public bool $searchable = true,
        public bool $orderable = true,
    ) {
    }
}
