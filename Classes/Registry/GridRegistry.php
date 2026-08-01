<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Registry;

use HRR\T3Datatable\Contract\GridInterface;
use HRR\T3Datatable\DataTable\GridDefinition;
use HRR\T3Datatable\Exception\GridNotFoundException;
use HRR\T3Datatable\Exception\InvalidColumnException;

/**
 * Collects all grids tagged with `t3datatable.grid`.
 *
 * @internal
 */
final class GridRegistry
{
    /** @var array<string, GridInterface> */
    private array $indexed = [];

    /**
     * @param iterable<GridInterface> $grids
     */
    public function __construct(iterable $grids)
    {
        foreach ($grids as $grid) {
            $identifier = $grid->getIdentifier();
            if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_.-]*$/', $identifier)) {
                throw new \InvalidArgumentException(sprintf('Invalid grid identifier "%s".', $identifier));
            }
            if (isset($this->indexed[$identifier])) {
                throw new \InvalidArgumentException(sprintf('DataTable grid "%s" is registered more than once.', $identifier));
            }
            $this->indexed[$identifier] = $grid;
        }
    }

    public function has(string $identifier): bool
    {
        return isset($this->indexed[$identifier]);
    }

    public function get(string $identifier): GridInterface
    {
        if (!isset($this->indexed[$identifier])) {
            throw new GridNotFoundException(sprintf('DataTable grid "%s" is not registered.', $identifier));
        }

        return $this->indexed[$identifier];
    }

    public function resolveDefinition(GridInterface $grid): GridDefinition
    {
        $definition = new GridDefinition();
        $grid->build($definition);

        if ($definition->getColumns() === []) {
            throw new GridNotFoundException(sprintf('DataTable grid "%s" declares no columns.', $grid->getIdentifier()));
        }

        if (!preg_match('/^[a-zA-Z0-9_.]+$/', $grid->getTableName())) {
            throw new InvalidColumnException(sprintf('Invalid table name "%s".', $grid->getTableName()));
        }
        $allowlist = new \HRR\T3Datatable\Security\ColumnAllowlist($definition);
        foreach ($definition->getColumns() as $column) {
            $allowlist->assertDeclared($column->name);
        }
        $defaultOrderColumn = $definition->getDefaultOrderColumn();
        if ($defaultOrderColumn !== null) {
            $allowlist->assertOrderable($defaultOrderColumn);
        }

        return $definition;
    }
}
