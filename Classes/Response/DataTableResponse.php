<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Response;

/**
 * Builds the DataTables-compatible JSON payload.
 *
 * @internal
 */
final class DataTableResponse
{
    /**
     * @param list<array<string, mixed>> $rows
     *
     * @return array{draw: int, recordsTotal: int, recordsFiltered: int, data: list<array<string, mixed>>}
     */
    public function build(int $draw, int $recordsTotal, int $recordsFiltered, array $rows): array
    {
        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ];
    }
}
