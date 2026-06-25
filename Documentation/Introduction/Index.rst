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

|extension_name| provides a **server-side DataTables-compatible engine** for TYPO3
backend modules:

- **Search** — global and per-column keyword search
- **Sort** — column ordering with allowlist validation
- **Paginate** — offset/limit server-side
- **Secure** — backend-user gate, parameter binding, column allowlists

Any extension can register a :php:`GridInterface` implementation, tag it in
Symfony DI, and use the shared AJAX route
:file:`Configuration/Backend/AjaxRoutes.php` (``/t3datatable/data``).

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

The bundled ES module :js:`@hrr/t3-datatable/datatable-backend.js` speaks the
DataTables server-side JSON protocol and uses TYPO3's
:js:`@typo3/core/ajax/ajax-request` (backend-only).

Requirements
============

- TYPO3 **13.4** or **14.3**
- PHP **8.2** or newer
- Backend module context (not for public frontend pages)

What it is not
==============

- Not a full port of `Laravel DataTables <https://github.com/yajra/laravel-datatables>`_ —
  it is TYPO3-native (Doctrine DBAL, PSR-7, backend routes).
- Not a replacement for the core List module or workspace-aware record lists.
- Not a frontend AJAX table library (post-MVP scope).
