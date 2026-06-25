<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Tests\Functional\Engine;

use HRR\T3Datatable\DataTable\GridDefinition;
use HRR\T3Datatable\Engine\QueryEngine;
use HRR\T3Datatable\Request\DataTableRequest;
use HRR\T3Datatable\Response\DataTableResponse;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class QueryEngineTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = [
        'backend',
        'beuser',
    ];

    protected array $testExtensionsToLoad = [
        'hrr/t3-datatable',
    ];

    #[Test]
    public function returnsDataTablesCompatiblePayloadForBeUsers(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');
        $this->setUpBackendUser(1);

        $definition = (new GridDefinition())
            ->addColumn('uid', 'UID', searchable: false, orderable: true)
            ->addColumn('username', 'Username', searchable: true, orderable: true)
            ->setDefaultOrder('uid', 'ASC');

        $request = new DataTableRequest(
            draw: 1,
            start: 0,
            length: 10,
            globalSearch: '',
            columns: [
                ['index' => 0, 'data' => 'uid', 'name' => 'uid', 'searchable' => false, 'orderable' => true, 'searchValue' => ''],
                ['index' => 1, 'data' => 'username', 'name' => 'username', 'searchable' => true, 'orderable' => true, 'searchValue' => ''],
            ],
            orders: [],
        );

        $engine = new QueryEngine(
            $this->get(ConnectionPool::class),
            new DataTableResponse(),
        );

        $payload = $engine->process('be_users', $definition, $request);

        self::assertSame(1, $payload['draw']);
        self::assertGreaterThanOrEqual(1, $payload['recordsTotal']);
        self::assertGreaterThanOrEqual(1, $payload['recordsFiltered']);
        self::assertNotEmpty($payload['data']);
        self::assertArrayHasKey('username', $payload['data'][0]);
    }

    #[Test]
    public function globalSearchFiltersRowsCaseInsensitively(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/pages.csv');

        $definition = (new GridDefinition())
            ->addColumn('uid', 'UID', searchable: false, orderable: true)
            ->addColumn('title', 'Title', searchable: true, orderable: true)
            ->setDefaultOrder('uid', 'ASC');

        $request = new DataTableRequest(
            draw: 1,
            start: 0,
            length: 10,
            globalSearch: 'ABOUT',
            columns: [
                ['index' => 0, 'data' => 'uid', 'name' => 'uid', 'searchable' => false, 'orderable' => true, 'searchValue' => ''],
                ['index' => 1, 'data' => 'title', 'name' => 'title', 'searchable' => true, 'orderable' => true, 'searchValue' => ''],
            ],
            orders: [],
        );

        $engine = new QueryEngine(
            $this->get(ConnectionPool::class),
            new DataTableResponse(),
        );

        $payload = $engine->process('pages', $definition, $request);

        self::assertSame(3, $payload['recordsTotal']);
        self::assertSame(1, $payload['recordsFiltered']);
        self::assertCount(1, $payload['data']);
        self::assertSame('About', $payload['data'][0]['title']);
    }
}
