<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Tests\Functional\Controller;

use HRR\T3Datatable\Controller\DataTableController;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class DataTableControllerTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = [
        'backend',
        'beuser',
    ];

    protected array $testExtensionsToLoad = [
        'hrr/t3-datatable',
    ];

    #[Test]
    public function dataActionReturnsJsonForRegisteredPagesDemoGrid(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/pages.csv');
        $this->setUpBackendUser(1);

        $request = (new ServerRequest())
            ->withQueryParams(['grid' => 'demo_pages'])
            ->withParsedBody([
                'draw' => 1,
                'start' => 0,
                'length' => 1,
                'search' => ['value' => '', 'regex' => false],
                'columns' => [
                    ['data' => 'uid', 'name' => 'uid', 'searchable' => 'false', 'orderable' => 'true', 'search' => ['value' => '']],
                    ['data' => 'title', 'name' => 'title', 'searchable' => 'true', 'orderable' => 'true', 'search' => ['value' => '']],
                ],
                'order' => [],
            ]);

        $response = $this->get(DataTableController::class)->dataAction($request);
        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame(1, $payload['draw']);
        self::assertArrayHasKey('recordsTotal', $payload);
        self::assertArrayHasKey('recordsFiltered', $payload);
        self::assertIsArray($payload['data']);
        self::assertCount(1, $payload['data']);
    }

    #[Test]
    public function dataActionRejectsUnknownGrid(): void
    {
        $this->importCSVDataSet(__DIR__ . '/../../Fixtures/be_users.csv');
        $this->setUpBackendUser(1);

        $request = (new ServerRequest())
            ->withQueryParams(['grid' => 'missing_grid'])
            ->withParsedBody(['draw' => 1, 'start' => 0, 'length' => 10]);

        $response = $this->get(DataTableController::class)->dataAction($request);
        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(400, $response->getStatusCode());
        self::assertArrayHasKey('error', $payload);
    }
}
