.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

.. contents::
   :local:
   :depth: 2

What it does
============

T3 DataTable gives your TYPO3 backend modules a data grid that does the
search, sort, and paging work on the server. It speaks the same JSON protocol
as the DataTables library, so the browser only ever loads one page of rows at a
time.

Here is what you get out of the box:

- Global search across every searchable column you declared (the shipped UI
  exposes one search box; per-column search values are supported by the AJAX
  protocol if your own JavaScript sends them).
- Sort by any column you allow. Each column name is checked against an
  allowlist before it reaches the query.
- Page through large tables on the server, so the browser never has to hold the
  whole result set.
- Every request runs behind a backend-user check, binds all values as query
  parameters, and only touches the columns you declared.

Any extension can register a :php:`GridInterface` implementation, tag it in
Symfony DI, and reuse the shared AJAX route in
:file:`Configuration/Backend/AjaxRoutes.php` (``/t3datatable/data``). You do not
need to write your own endpoint.

Architecture
============

.. code-block:: text

   Consumer extension (GridInterface)
        ↓ tagged t3datatable.grid
   GridRegistry
        ↓
   DataTableController (AJAX)
        ↓
   DataTableRequest → QueryEngine → JsonResponse

The bundled ES module :js:`@hrr/t3-datatable/datatable-backend.js` talks to that
endpoint using TYPO3's own :js:`@typo3/core/ajax/ajax-request`, so it works in
the backend only.

Requirements
============

- TYPO3 13 LTS or 14
- PHP 8.2 or newer
- A backend module context. It is not meant for public frontend pages.

What it is not
==============

A few things this extension does not try to be, so you know when to reach for
something else:

- It is not a full port of `Laravel DataTables <https://github.com/yajra/laravel-datatables>`_.
  It is built for TYPO3 with Doctrine DBAL, PSR-7, and backend routes.
- It is not a replacement for the core :guilabel:`Web > List` module
  (:php:`TYPO3\\CMS\\Backend\\RecordList\\DatabaseRecordList`). That module
  already lists TCA records on a page with search, pagination, and editing.
  T3 DataTable targets **custom backend modules** that need a DataTables-style
  server-side grid API over a table, without building your own AJAX endpoint.
  It also does not replace workspace-aware record lists.
- It is not a frontend AJAX table library. Frontend use is out of scope for now.
