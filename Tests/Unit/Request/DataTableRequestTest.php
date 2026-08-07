<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Tests\Unit\Request;

use HRR\T3Datatable\Request\DataTableRequest;
use HRR\T3Datatable\Exception\InvalidRequestException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Http\ServerRequest;

final class DataTableRequestTest extends TestCase
{
    #[Test]
    public function parsesStandardDataTablesParameters(): void
    {
        $request = (new ServerRequest())->withParsedBody([
            'draw' => '2',
            'start' => '10',
            'length' => '25',
            'search' => ['value' => 'admin', 'regex' => 'false'],
            'columns' => [
                ['data' => 'username', 'name' => 'username', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '']],
                ['data' => 'email', 'name' => 'email', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => 't3']],
            ],
            'order' => [
                ['column' => '0', 'dir' => 'desc'],
            ],
        ]);

        $parsed = DataTableRequest::fromRequest($request);

        self::assertSame(2, $parsed->draw);
        self::assertSame(10, $parsed->start);
        self::assertSame(25, $parsed->length);
        self::assertSame('admin', $parsed->globalSearch);
        self::assertCount(2, $parsed->columns);
        self::assertSame('username', $parsed->resolveOrderColumnName());
        self::assertSame('DESC', $parsed->resolveOrderDirection());
    }

    #[Test]
    public function preservesColumnIndexesAndResolvesMultipleOrderClauses(): void
    {
        $request = (new ServerRequest())->withParsedBody([
            'columns' => [
                1 => ['data' => 'title', 'search' => ['value' => '']],
                3 => ['data' => 'uid', 'search' => ['value' => '']],
            ],
            'order' => [
                ['column' => 3, 'dir' => 'desc'],
                ['column' => 1, 'dir' => 'asc'],
            ],
        ]);

        $parsed = DataTableRequest::fromRequest($request);

        self::assertSame('uid', $parsed->resolveOrderColumnName());
        self::assertSame([
            ['name' => 'uid', 'direction' => 'DESC'],
            ['name' => 'title', 'direction' => 'ASC'],
        ], $parsed->resolveOrderings());
    }

    #[Test]
    public function rejectsUnboundedPageRequests(): void
    {
        $request = (new ServerRequest())->withParsedBody(['length' => 0]);

        $this->expectException(InvalidRequestException::class);
        DataTableRequest::fromRequest($request);
    }
}
