.. include:: /Includes.rst.txt

.. _developer:

===============
Developer guide
===============

.. contents::
   :local:
   :depth: 2

.. toctree::
   :maxdepth: 2
   :titlesonly:

   GridRegistration
   JavaScriptIntegration

Quick start
===========

1. ``composer require hrr/t3-datatable``
2. Tag :php:`GridInterface` implementations in your :file:`Services.yaml`
3. Implement a grid class (table + columns)
4. Load the JS module in your backend module template
5. Call :js:`initDataTable()` with your grid identifier

Core contract
=============

.. code-block:: php

   namespace HRR\T3Datatable\Contract;

   interface GridInterface
   {
       public function getIdentifier(): string;
       public function getTableName(): string;
       public function isAccessible(BackendUserAuthentication $backendUser): bool;
       public function build(GridDefinition $definition): void;
   }

``isAccessible()`` is mandatory. It must enforce the module, table, and
record-level permissions appropriate to the grid; authentication alone does not
authorize a user to read every registered grid.

AJAX endpoint
=============

Every grid shares one route, so you never have to register your own AJAX
endpoint:

.. list-table::
   :header-rows: 1
   :widths: 25 75

   * - Route key
     - ``t3datatable_data``
   * - Path
     - ``/t3datatable/data``
   * - Query param
     - ``grid={your_grid_identifier}``
   * - Controller
     - :php:`HRR\T3Datatable\Controller\DataTableController::dataAction`

In JavaScript, use ``TYPO3.settings.ajaxUrls.t3datatable_data``.

Response shape
==============

.. code-block:: json

   {
     "draw": 1,
     "recordsTotal": 150,
     "recordsFiltered": 42,
     "data": [
       {"uid": 1, "title": "Example"}
     ]
   }

PHP classes
===========

.. list-table::
   :header-rows: 1
   :widths: 35 65

   * - Class
     - Role
   * - :php:`GridRegistry`
     - Resolves grids by identifier via DI tag
   * - :php:`DataTableRequest`
     - Parses DataTables AJAX parameters
   * - :php:`QueryEngine`
     - Builds and executes Doctrine queries
   * - :php:`ColumnAllowlist`
     - Validates searchable/orderable columns
   * - :php:`DataTableResponse`
     - Builds JSON payload

Compatibility policy
====================

Only ``GridInterface``, ``GridDefinition``, ``ColumnDefinition``, and the
documented JavaScript exports are public API. Other PHP classes are internal
implementation details. Minor releases add backwards-compatible functionality;
deprecated public API remains until the next major release and is recorded in
the changelog and upgrade guide.
