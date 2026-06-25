<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Request;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Parsed DataTables server-side request parameters.
 */
final readonly class DataTableRequest
{
    /**
     * @param list<array{data: string, name: string, searchable: bool, orderable: bool, searchValue: string}> $columns
     * @param list<array{columnIndex: int, direction: string}> $orders
     */
    public function __construct(
        public int $draw,
        public int $start,
        public int $length,
        public string $globalSearch,
        public array $columns,
        public array $orders,
    ) {
    }

    public static function fromRequest(ServerRequestInterface $request): self
    {
        $params = array_replace(
            $request->getQueryParams(),
            (array) $request->getParsedBody(),
        );

        $draw = max(0, (int) ($params['draw'] ?? 0));
        $start = max(0, (int) ($params['start'] ?? 0));
        $length = (int) ($params['length'] ?? 10);
        if ($length < 0) {
            $length = 10;
        }

        $search = $params['search'] ?? [];
        $globalSearch = '';
        if (is_array($search)) {
            $globalSearch = trim((string) ($search['value'] ?? ''));
        }

        $columns = [];
        $rawColumns = $params['columns'] ?? [];
        if (is_array($rawColumns)) {
            foreach ($rawColumns as $index => $column) {
                if (!is_array($column)) {
                    continue;
                }
                $columnSearch = $column['search'] ?? [];
                $searchValue = '';
                if (is_array($columnSearch)) {
                    $searchValue = trim((string) ($columnSearch['value'] ?? ''));
                }
                $data = trim((string) ($column['data'] ?? ''));
                $name = trim((string) ($column['name'] ?? $data));
                if ($data === '' && $name !== '') {
                    $data = $name;
                }
                $columns[] = [
                    'index' => (int) $index,
                    'data' => $data,
                    'name' => $name,
                    'searchable' => self::toBool($column['searchable'] ?? true),
                    'orderable' => self::toBool($column['orderable'] ?? true),
                    'searchValue' => $searchValue,
                ];
            }
        }

        $orders = [];
        $rawOrders = $params['order'] ?? [];
        if (is_array($rawOrders)) {
            foreach ($rawOrders as $order) {
                if (!is_array($order)) {
                    continue;
                }
                $orders[] = [
                    'columnIndex' => (int) ($order['column'] ?? 0),
                    'direction' => strtolower((string) ($order['dir'] ?? 'asc')) === 'desc' ? 'DESC' : 'ASC',
                ];
            }
        }

        return new self($draw, $start, $length, $globalSearch, $columns, $orders);
    }

    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $normalized = strtolower(trim((string) $value));

        return !in_array($normalized, ['0', 'false', 'no', ''], true);
    }

    public function resolveOrderColumnName(): ?string
    {
        if ($this->orders === []) {
            return null;
        }

        $first = $this->orders[0];
        $index = $first['columnIndex'];
        if (!isset($this->columns[$index])) {
            return null;
        }

        $name = $this->columns[$index]['data'] ?: $this->columns[$index]['name'];

        return $name !== '' ? $name : null;
    }

    public function resolveOrderDirection(): string
    {
        if ($this->orders === []) {
            return 'ASC';
        }

        return $this->orders[0]['direction'];
    }
}
