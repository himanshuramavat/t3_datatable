<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Registry;

use HRR\T3Datatable\Contract\GridInterface;
use HRR\T3Datatable\DataTable\GridDefinition;
use HRR\T3Datatable\Exception\GridNotFoundException;

/**
 * Collects all grids tagged with `t3datatable.grid`.
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
            $this->indexed[$grid->getIdentifier()] = $grid;
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

        return $definition;
    }
}
