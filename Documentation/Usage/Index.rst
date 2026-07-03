.. include:: /Includes.rst.txt

.. _usage:

=====
Usage
=====

.. contents::
   :local:
   :depth: 2

Demo backend module
===================

After installation, open:

:guilabel:`Admin Tools > T3 DataTable > Demo grid`

to see a working server-side grid using the ``pages`` table.

This module lists ``pages`` via the ``demo_pages`` grid identifier. It
demonstrates:

- Module registration in :file:`Configuration/Backend/Modules.php`
- Grid class :php:`HRR\T3Datatable\Demo\PagesGrid`
- Fluid template :file:`Resources/Private/Templates/Backend/Demo/Index.html`
- CSP-safe JavaScript auto-initialisation via ``data-*`` attributes

AJAX data endpoint
==================

Any registered grid can be queried directly (for debugging):

.. code-block:: text

   POST /typo3/ajax/t3datatable/data?grid=demo_pages

Parameters follow the
`DataTables server-side protocol <https://datatables.net/manual/server-side>`_.

Typical workflow for integrators
================================

1. Install and activate the extension.
2. Open the demo module to confirm AJAX + rendering works.
3. Implement your own :php:`GridInterface` in your extension.
4. Add the grid to your backend module template.
