<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Controller;

use HRR\T3Datatable\Engine\QueryEngine;
use HRR\T3Datatable\Exception\GridNotFoundException;
use HRR\T3Datatable\Exception\InvalidColumnException;
use HRR\T3Datatable\Registry\GridRegistry;
use HRR\T3Datatable\Request\DataTableRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Http\JsonResponse;

/**
 * Central AJAX endpoint for all registered DataTable grids.
 */
final class DataTableController
{
    public function __construct(
        private readonly GridRegistry $gridRegistry,
        private readonly QueryEngine $queryEngine,
    ) {
    }

    public function dataAction(ServerRequestInterface $request): ResponseInterface
    {
        if (!$this->isBackendUserAuthenticated()) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        $gridId = trim((string) ($request->getQueryParams()['grid'] ?? ''));
        if ($gridId === '') {
            return new JsonResponse(['error' => 'Missing grid identifier.'], 400);
        }

        try {
            $grid = $this->gridRegistry->get($gridId);
            $definition = $this->gridRegistry->resolveDefinition($grid);
            $dataTableRequest = DataTableRequest::fromRequest($request);
            $payload = $this->queryEngine->process(
                $grid->getTableName(),
                $definition,
                $dataTableRequest,
            );

            return new JsonResponse($payload);
        } catch (GridNotFoundException|InvalidColumnException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        }
    }

    private function isBackendUserAuthenticated(): bool
    {
        $user = $GLOBALS['BE_USER'] ?? null;

        return $user instanceof BackendUserAuthentication && $user->user !== null;
    }
}
