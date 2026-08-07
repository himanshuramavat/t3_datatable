.. include:: /Includes.rst.txt

=========================
Backend JavaScript module
=========================

.. contents::
   :local:
   :depth: 2

Scope
=====

The ES module :js:`@hrr/t3-datatable/datatable-backend.js` runs **only in the
TYPO3 backend**. It depends on:

- TYPO3 import maps (:file:`Configuration/JavaScriptModules.php`)
- :js:`@typo3/core/ajax/ajax-request` (CSRF + backend session)
- ``TYPO3.settings.ajaxUrls.t3datatable_data``

Public frontend pages do not have this environment.

Recommended: data-attribute auto-initialization (CSP-safe)
==========================================================

The TYPO3 backend enforces a strict :doc:`Content Security Policy <t3coreapi:ApiOverview/ContentSecurityPolicy/Index>`
that blocks inline ``<script>`` execution. Do not put an inline
``<script type="module">`` in your Fluid template. The browser will reject it
with a ``script-src`` violation.

Instead, load the module from your controller and let it auto-initialize from
``data-*`` attributes on the table:

.. code-block:: php
   :caption: Backend controller

   $this->pageRenderer->loadJavaScriptModule('@hrr/t3-datatable/datatable-backend.js');

Load the bundled JavaScript language labels too, otherwise the controls fall
back to English:

.. code-block:: php

   $this->pageRenderer->addInlineLanguageLabelFile(
       'EXT:t3_datatable/Resources/Private/Language/locallang_js.xlf',
   );

.. code-block:: html
   :caption: Fluid template, no inline script required

   <table id="my-records-table"
          class="table table-striped"
          data-t3-datatable="myext_records"
          data-columns="{columnsJson}"
          data-page-length="25">
     <thead>
       <tr>
         <th>UID</th>
         <th>Title</th>
       </tr>
     </thead>
     <tbody></tbody>
   </table>

Assign ``columnsJson`` in the controller as an HTML-escaped JSON string
(``json_encode()`` + Fluid's default escaping). On ``DocumentService.ready()``,
the module scans every ``table[data-t3-datatable]`` element and initializes it.

Supported ``data-*`` attributes:

.. list-table::
   :header-rows: 1
   :widths: 30 70

   * - Attribute
     - Description
   * - ``data-t3-datatable``
     - Required. Grid identifier (matches :php:`GridInterface::getIdentifier()`)
   * - ``data-columns``
     - Required. JSON array of ``{ data, title, searchable?, orderable? }``.
       Cannot express ``render`` / ``virtual`` / ``html`` (use programmatic
       :js:`initDataTable()` for those)
   * - ``data-page-length``
     - Rows per page (default ``25``)
   * - ``data-search-placeholder``
     - Placeholder for the search input

Advanced: programmatic initialization
======================================

If you load your own CSP-compliant module (e.g. a dedicated module file shipped
in your extension, *not* an inline script), you can call :js:`initDataTable()`
directly:

.. code-block:: javascript

   import { initDataTable } from '@hrr/t3-datatable/datatable-backend.js';

   initDataTable('#my-records-table', {
     gridIdentifier: 'myext_records',
     columns: [
       { data: 'uid', title: 'UID', orderable: true, searchable: false },
       { data: 'title', title: 'Title', orderable: true, searchable: true },
     ],
     pageLength: 25,
   });

Options
=======

.. list-table::
   :header-rows: 1
   :widths: 25 75

   * - Option
     - Description
   * - ``gridIdentifier``
     - Required. Matches :php:`GridInterface::getIdentifier()`
   * - ``columns``
     - Required. Array of column objects (see below)
   * - ``pageLength``
     - Rows per page (default ``25``)
   * - ``searchPlaceholder``
     - Placeholder for the search input
   * - ``onRowsRendered``
     - Optional. ``(tbody, rows) => void`` called after each successful draw
       (useful for selection UI, tooltips, or event wiring on AJAX rows)

Column object
=============

Each entry in ``columns`` may include:

.. list-table::
   :header-rows: 1
   :widths: 25 75

   * - Property
     - Description
   * - ``data``
     - Required. Field name from the AJAX row (or a client-only key when
       ``virtual`` is ``true``)
   * - ``title``
     - Header label
   * - ``searchable``
     - Included in global search when ``true`` (default ``true``). Ignored for
       virtual columns
   * - ``orderable``
     - Clickable sort header when ``true`` (default ``true``)
   * - ``virtual``
     - Client-only column. Omitted from the AJAX payload and from SQL. Use for
       checkboxes, action buttons, or other UI that is not a DB field. Order
       indices are remapped automatically
   * - ``html``
     - When ``true``, a string returned from ``render`` is assigned with
       ``innerHTML``. Default is text (``textContent``)
   * - ``render``
     - Optional. ``(value, row, td) => string | Node | null``. Formats the cell.
       Return a ``Node`` to append DOM, a ``string`` for text/HTML, or ``null``
       if you mutate ``td`` yourself (e.g. async icon loading)

Example with a virtual actions column:

.. code-block:: javascript

   initDataTable('#my-records-table', {
     gridIdentifier: 'myext_records',
     columns: [
       { data: 'uid', title: 'UID', searchable: false },
       { data: 'title', title: 'Title' },
       {
         data: '_actions',
         title: '',
         virtual: true,
         orderable: false,
         searchable: false,
         render: (_value, row) => {
           const button = document.createElement('button');
           button.type = 'button';
           button.className = 'btn btn-default btn-sm';
           button.dataset.uid = String(row.uid);
           button.textContent = 'Delete';
           return button;
         },
       },
     ],
     onRowsRendered: (tbody) => {
       // Wire listeners on newly drawn rows if needed
     },
   });

.. note::

   ``data-*`` auto-initialization can only pass JSON-serializable column
   options (``data``, ``title``, ``searchable``, ``orderable``). Use
   programmatic :js:`initDataTable()` when you need ``render``, ``virtual``,
   ``html``, or ``onRowsRendered``.

Return value
============

:js:`initDataTable()` returns ``{ reload: () => Promise }`` for manual refresh.

Protocol
========

The module POSTs DataTables-compatible parameters (``draw``, ``start``, ``length``,
``search``, ``columns``, ``order``) to the shared AJAX endpoint. The server
responds with ``recordsTotal``, ``recordsFiltered``, and ``data``.
