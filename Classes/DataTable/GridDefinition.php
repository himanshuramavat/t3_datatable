<?php

declare(strict_types=1);

namespace HRR\T3Datatable\DataTable;

/**
 * Fluent builder passed to {@see \HRR\T3Datatable\Contract\GridInterface::build()}.
 */
final class GridDefinition
{
    /** @var list<ColumnDefinition> */
    private array $columns = [];

    private ?string $defaultOrderColumn = null;

    private string $defaultOrderDirection = 'ASC';

    private int $defaultPageLength = 25;

    private bool $withDeletedRestriction = false;

    private bool $withHiddenRestriction = false;

    public function addColumn(
        string $name,
        string $label,
        bool $searchable = true,
        bool $orderable = true,
    ): self {
        $this->columns[] = new ColumnDefinition($name, $label, $searchable, $orderable);

        return $this;
    }

    public function setDefaultOrder(string $column, string $direction = 'ASC'): self
    {
        $this->defaultOrderColumn = $column;
        $this->defaultOrderDirection = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        return $this;
    }

    public function setPageLength(int $length): self
    {
        $this->defaultPageLength = max(1, $length);

        return $this;
    }

    public function withDeletedRestriction(): self
    {
        $this->withDeletedRestriction = true;

        return $this;
    }

    public function withHiddenRestriction(): self
    {
        $this->withHiddenRestriction = true;

        return $this;
    }

    /**
     * @return list<ColumnDefinition>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getDefaultOrderColumn(): ?string
    {
        return $this->defaultOrderColumn;
    }

    public function getDefaultOrderDirection(): string
    {
        return $this->defaultOrderDirection;
    }

    public function getDefaultPageLength(): int
    {
        return $this->defaultPageLength;
    }

    public function appliesDeletedRestriction(): bool
    {
        return $this->withDeletedRestriction;
    }

    public function appliesHiddenRestriction(): bool
    {
        return $this->withHiddenRestriction;
    }

    public function findColumn(string $name): ?ColumnDefinition
    {
        foreach ($this->columns as $column) {
            if ($column->name === $name) {
                return $column;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public function getSearchableColumnNames(): array
    {
        return array_values(array_map(
            static fn (ColumnDefinition $column): string => $column->name,
            array_filter($this->columns, static fn (ColumnDefinition $column): bool => $column->searchable),
        ));
    }

    /**
     * @return list<string>
     */
    public function getOrderableColumnNames(): array
    {
        return array_values(array_map(
            static fn (ColumnDefinition $column): string => $column->name,
            array_filter($this->columns, static fn (ColumnDefinition $column): bool => $column->orderable),
        ));
    }
}
