<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Request;

use HRR\T3Datatable\Exception\InvalidRequestException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Parsed DataTables server-side request parameters.
 *
 * @internal
 */
final readonly class DataTableRequest
{
    public const MAX_COLUMNS = 100;

    public const MAX_ORDERS = 5;

    public const MAX_START = 1_000_000;

    public const MAX_SEARCH_LENGTH = 255;

    /**
     * @param array<int, array{data: string, name: string, searchable: bool, orderable: bool, searchValue: string}> $columns
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
        if ($start > self::MAX_START) {
            throw new InvalidRequestException(sprintf('The start parameter must not exceed %d.', self::MAX_START));
        }
        $length = (int) ($params['length'] ?? 10);
        if ($length < 1) {
            throw new InvalidRequestException('The length parameter must be a positive integer.');
        }

        $search = $params['search'] ?? [];
        $globalSearch = '';
        if (is_array($search)) {
            $globalSearch = self::normalizeSearchValue($search['value'] ?? '');
        }

        $columns = [];
        $rawColumns = $params['columns'] ?? [];
        if (is_array($rawColumns)) {
            if (count($rawColumns) > self::MAX_COLUMNS) {
                throw new InvalidRequestException(sprintf('A request may contain at most %d columns.', self::MAX_COLUMNS));
            }
            foreach ($rawColumns as $index => $column) {
                if (!is_int($index) && !ctype_digit((string) $index)) {
                    throw new InvalidRequestException('Column indexes must be integers.');
                }
                if (!is_array($column)) {
                    throw new InvalidRequestException('Each column must be an object.');
                }
                $columnSearch = $column['search'] ?? [];
                $searchValue = '';
                if (is_array($columnSearch)) {
                    $searchValue = self::normalizeSearchValue($columnSearch['value'] ?? '');
                }
                $data = trim((string) ($column['data'] ?? ''));
                $name = trim((string) ($column['name'] ?? $data));
                if ($data === '' && $name !== '') {
                    $data = $name;
                }
                $columns[(int) $index] = [
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
            if (count($rawOrders) > self::MAX_ORDERS) {
                throw new InvalidRequestException(sprintf('A request may contain at most %d order clauses.', self::MAX_ORDERS));
            }
            foreach ($rawOrders as $order) {
                if (!is_array($order)) {
                    throw new InvalidRequestException('Each order clause must be an object.');
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

    private static function normalizeSearchValue(mixed $value): string
    {
        $value = trim((string) $value);
        if (mb_strlen($value) > self::MAX_SEARCH_LENGTH) {
            throw new InvalidRequestException(sprintf('Search values must not exceed %d characters.', self::MAX_SEARCH_LENGTH));
        }

        return $value;
    }

    public function resolveOrderColumnName(): ?string
    {
        if ($this->orders === []) {
            return null;
        }

        $first = $this->orders[0];
        $index = $first['columnIndex'];
        if (!array_key_exists($index, $this->columns)) {
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

    /**
     * @return list<array{name: string, direction: 'ASC'|'DESC'}>
     */
    public function resolveOrderings(): array
    {
        $orderings = [];
        foreach ($this->orders as $order) {
            $column = $this->columns[$order['columnIndex']] ?? null;
            if ($column === null) {
                continue;
            }
            $name = $column['data'] ?: $column['name'];
            if ($name === '' || isset($orderings[$name])) {
                continue;
            }
            $orderings[$name] = ['name' => $name, 'direction' => $order['direction']];
        }

        return array_values($orderings);
    }
}
