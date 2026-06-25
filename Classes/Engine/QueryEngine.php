<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Engine;

use HRR\T3Datatable\DataTable\GridDefinition;
use HRR\T3Datatable\Request\DataTableRequest;
use HRR\T3Datatable\Response\DataTableResponse;
use HRR\T3Datatable\Security\ColumnAllowlist;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * Doctrine DBAL query engine for server-side DataTables processing.
 */
final class QueryEngine
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly DataTableResponse $responseBuilder,
    ) {
    }

    /**
     * @return array{draw: int, recordsTotal: int, recordsFiltered: int, data: list<array<string, mixed>>}
     */
    public function process(
        string $tableName,
        GridDefinition $definition,
        DataTableRequest $request,
    ): array {
        $allowlist = new ColumnAllowlist($definition);

        $totalCount = $this->countRows($tableName, $definition, $request, applySearch: false);

        $filteredQb = $this->createBaseQueryBuilder($tableName, $definition);
        $this->applySearch($filteredQb, $definition, $request, $allowlist);
        $filteredCount = $this->executeCount($filteredQb);

        $dataQb = $this->createBaseQueryBuilder($tableName, $definition);
        $this->applySearch($dataQb, $definition, $request, $allowlist);
        $this->applyOrdering($dataQb, $definition, $request, $allowlist);
        $this->applyPagination($dataQb, $request);

        $selectFields = array_map(
            static fn ($column) => $column->name,
            $definition->getColumns(),
        );
        if ($selectFields !== []) {
            $dataQb->select(...$selectFields);
        } else {
            $dataQb->select('*');
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = $dataQb->executeQuery()->fetchAllAssociative();

        return $this->responseBuilder->build(
            $request->draw,
            $totalCount,
            $filteredCount,
            $rows,
        );
    }

    private function countRows(
        string $tableName,
        GridDefinition $definition,
        DataTableRequest $request,
        bool $applySearch,
    ): int {
        $qb = $this->createBaseQueryBuilder($tableName, $definition);
        if ($applySearch) {
            $allowlist = new ColumnAllowlist($definition);
            $this->applySearch($qb, $definition, $request, $allowlist);
        }

        return $this->executeCount($qb);
    }

    private function createBaseQueryBuilder(string $tableName, GridDefinition $definition): QueryBuilder
    {
        $qb = $this->connectionPool->getQueryBuilderForTable($tableName);
        $qb->getRestrictions()->removeAll();
        $qb->from($tableName);

        if ($definition->appliesDeletedRestriction()) {
            $qb->andWhere(
                $qb->expr()->eq('deleted', $qb->createNamedParameter(0, Connection::PARAM_INT)),
            );
        }
        if ($definition->appliesHiddenRestriction()) {
            $qb->andWhere(
                $qb->expr()->eq('hidden', $qb->createNamedParameter(0, Connection::PARAM_INT)),
            );
        }

        return $qb;
    }

    private function applySearch(
        QueryBuilder $qb,
        GridDefinition $definition,
        DataTableRequest $request,
        ColumnAllowlist $allowlist,
    ): void {
        if ($request->globalSearch !== '') {
            $keyword = '%' . mb_strtolower($request->globalSearch) . '%';
            $or = [];
            foreach ($definition->getSearchableColumnNames() as $column) {
                $allowlist->assertSearchable($column);
                $or[] = $qb->expr()->comparison(
                    'LOWER(' . $qb->quoteIdentifier($column) . ')',
                    'LIKE',
                    $qb->createNamedParameter($keyword),
                );
            }
            if ($or !== []) {
                $qb->andWhere($qb->expr()->or(...$or));
            }
        }

        foreach ($request->columns as $column) {
            if (!$column['searchable'] || $column['searchValue'] === '') {
                continue;
            }
            $name = $column['data'] ?: $column['name'];
            if ($name === '') {
                continue;
            }
            $allowlist->assertSearchable($name);
            $keyword = '%' . mb_strtolower($column['searchValue']) . '%';
            $qb->andWhere(
                $qb->expr()->comparison(
                    'LOWER(' . $qb->quoteIdentifier($name) . ')',
                    'LIKE',
                    $qb->createNamedParameter($keyword),
                ),
            );
        }
    }

    private function applyOrdering(
        QueryBuilder $qb,
        GridDefinition $definition,
        DataTableRequest $request,
        ColumnAllowlist $allowlist,
    ): void {
        $orderColumn = $request->resolveOrderColumnName();
        $orderDirection = $request->resolveOrderDirection();

        if ($orderColumn === null) {
            $orderColumn = $definition->getDefaultOrderColumn();
            $orderDirection = $definition->getDefaultOrderDirection();
        }

        if ($orderColumn === null) {
            return;
        }

        $allowlist->assertOrderable($orderColumn);
        $qb->orderBy($orderColumn, $orderDirection);
    }

    private function applyPagination(QueryBuilder $qb, DataTableRequest $request): void
    {
        if ($request->length > 0) {
            $qb->setFirstResult($request->start);
            $qb->setMaxResults($request->length);
        }
    }

    private function executeCount(QueryBuilder $qb): int
    {
        $countQb = clone $qb;
        $countQb->resetOrderBy();
        $countQb->setFirstResult(0);
        $countQb->selectLiteral('COUNT(*) AS cnt');

        return (int) $countQb->executeQuery()->fetchOne();
    }
}
