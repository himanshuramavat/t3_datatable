.. include:: /Includes.rst.txt

================
Troubleshooting
================

.. contents::
   :local:
   :depth: 2

Empty table / zero records
==========================

- Confirm the grid identifier in JavaScript matches :php:`getIdentifier()`.
- Check the table name in :php:`getTableName()` exists and has data.
- If using ``withDeletedRestriction()`` or ``withHiddenRestriction()``, ensure
  those columns exist on the table.

``Invalid column name`` error
=============================

The client sent a column not declared on the grid, or a name failing the
allowlist. Only use alphanumeric column names with dots/underscores.

``Grid not registered`` / 400 response
======================================

- Implement :php:`GridInterface` in your extension.
- Add the ``t3datatable.grid`` tag via ``_instanceof`` in **your**
  :file:`Services.yaml` (the tag in |extension_name| alone does not apply to
  foreign packages).
- Flush caches after adding a new grid class.

``Unauthorized`` / 401
======================

The AJAX endpoint requires a logged-in backend user. Do not call it from the
public frontend without a separate route and security model.

JavaScript: ``ajaxUrls.t3datatable_data`` undefined
===================================================

- Activate :t3ext:`t3_datatable`.
- Flush all caches.
- Ensure you are in a backend module context (not frontend).

``Executing inline script violates ... Content Security Policy``
================================================================

The backend CSP blocks inline ``<script>`` tags. This happens if you place an
inline ``<script type="module">import { initDataTable } ...</script>`` directly
in a Fluid template.

**Fix:** remove the inline script. Load the module from the controller with
:php:`$pageRenderer->loadJavaScriptModule('@hrr/t3-datatable/datatable-backend.js')`
and configure the table via ``data-*`` attributes
(``data-t3-datatable``, ``data-columns``, ``data-page-length``). The module
auto-initializes on ``DocumentService.ready()``. See
:doc:`/Developer/JavaScriptIntegration`.

Adding a :file:`Configuration/ContentSecurityPolicies.php` mutation only helps
for **external** resources (e.g. a CDN host). It does not unblock inline
scripts without an ``'unsafe-inline'`` relaxation, which is discouraged.

Search returns no results on SQLite / case sensitivity
======================================================

Global search uses ``LOWER(column) LIKE`` for case-insensitive matching.
Verify searchable columns are declared with ``searchable: true``.

Render documentation locally
============================

From the package root (requires Docker, same as other TYPO3 extensions):

.. code-block:: bash

   composer doc-watch

Add a ``doc-watch`` script to :file:`composer.json` if you use the TYPO3
documentation renderer image (see :file:`packages/ns_aiuniverse/composer.json`).
