<?php

declare(strict_types=1);

namespace HRR\T3Datatable\Controller\Backend;

use HRR\T3Datatable\DataTable\ColumnDefinition;
use HRR\T3Datatable\DataTable\GridDefinition;
use HRR\T3Datatable\Registry\GridRegistry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Attribute\AsController;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Demo backend module showcasing the DataTable integration.
 */
#[AsController]
final class DemoController
{
    private const DEMO_GRID_IDENTIFIER = 'demo_pages';

    private const LOCALLANG_JS = 'EXT:t3_datatable/Resources/Private/Language/locallang_js.xlf';

    public function __construct(
        private readonly ModuleTemplateFactory $moduleTemplateFactory,
        private readonly PageRenderer $pageRenderer,
        private readonly GridRegistry $gridRegistry,
    ) {
    }

    public function indexAction(ServerRequestInterface $request): ResponseInterface
    {
        $this->pageRenderer->addInlineLanguageLabelFile(self::LOCALLANG_JS);
        $this->pageRenderer->addCssFile('EXT:t3_datatable/Resources/Public/Css/backend-module.css');
        $this->pageRenderer->loadJavaScriptModule('@hrr/t3-datatable/datatable-backend.js');

        $grid = $this->gridRegistry->get(self::DEMO_GRID_IDENTIFIER);
        $definition = $this->gridRegistry->resolveDefinition($grid);
        $columns = $this->buildViewColumns($definition);

        $view = $this->moduleTemplateFactory->create($request);
        $view->getDocHeaderComponent()->disable();
        $view->setTitle('T3 DataTable', 'Demo');

        $view->assignMultiple([
            'gridIdentifier' => $grid->getIdentifier(),
            'tableName' => $grid->getTableName(),
            'pageLength' => $definition->getDefaultPageLength(),
            'columnsJson' => json_encode($columns, JSON_THROW_ON_ERROR),
            'columns' => $columns,
        ]);

        return $view->renderResponse('Backend/Demo/Index');
    }

    /**
     * @return list<array{data: string, title: string, searchable: bool, orderable: bool}>
     */
    private function buildViewColumns(GridDefinition $definition): array
    {
        return array_map(
            static fn (ColumnDefinition $column): array => [
                'data' => $column->name,
                'title' => $column->label,
                'searchable' => $column->searchable,
                'orderable' => $column->orderable,
            ],
            $definition->getColumns(),
        );
    }
}
