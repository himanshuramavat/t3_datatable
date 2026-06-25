<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Security;

use HRR\T3Datatable\DataTable\GridDefinition;
use HRR\T3Datatable\Exception\InvalidColumnException;

/**
 * Validates column names against the grid definition allowlist.
 */
final class ColumnAllowlist
{
    public function __construct(
        private readonly GridDefinition $definition,
    ) {
    }

    public function assertSearchable(string $column): void
    {
        $this->assertValidIdentifier($column);
        if (!in_array($column, $this->definition->getSearchableColumnNames(), true)) {
            throw new InvalidColumnException(sprintf('Column "%s" is not searchable.', $column));
        }
    }

    public function assertOrderable(string $column): void
    {
        $this->assertValidIdentifier($column);
        if (!in_array($column, $this->definition->getOrderableColumnNames(), true)) {
            throw new InvalidColumnException(sprintf('Column "%s" is not orderable.', $column));
        }
    }

    public function assertDeclared(string $column): void
    {
        $this->assertValidIdentifier($column);
        if ($this->definition->findColumn($column) === null) {
            throw new InvalidColumnException(sprintf('Column "%s" is not declared on this grid.', $column));
        }
    }

    private function assertValidIdentifier(string $column): void
    {
        if ($column === '' || !preg_match('/^[a-zA-Z0-9_.]+$/', $column)) {
            throw new InvalidColumnException(sprintf('Invalid column name: "%s".', $column));
        }
    }
}
