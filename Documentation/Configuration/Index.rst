.. include:: /Includes.rst.txt

.. _configuration:

=============
Configuration
=============

.. contents::
   :local:
   :depth: 2

Overview
========

There is no global extension configuration here. T3 DataTable ships no
``ext_conf_template.txt``, and there are no options to set in the install tool.
You configure everything per grid inside :php:`GridInterface::build()`.

Service registration
======================

The extension ships these configuration files:

- :file:`Configuration/Services.yaml` sets up autowiring and the
  ``t3datatable.grid`` tag.
- :file:`Configuration/Backend/AjaxRoutes.php` registers the shared data
  endpoint.
- :file:`Configuration/JavaScriptModules.php` adds the ES module to the import
  map.
- :file:`Configuration/Backend/Modules.php` registers the demo backend module
  only.

Consumer extensions must add the ``_instanceof`` rule in their own
:file:`Configuration/Services.yaml`:

.. code-block:: yaml

   _instanceof:
     HRR\T3Datatable\Contract\GridInterface:
       tags: ['t3datatable.grid']

Grid definition options
=====================

Use :php:`GridDefinition` inside :php:`GridInterface::build()`:

.. code-block:: php

   $definition
       ->addColumn('uid', 'UID', searchable: false, orderable: true)
       ->addColumn('title', 'Title', searchable: true, orderable: true)
       ->setDefaultOrder('crdate', 'DESC')
       ->setPageLength(25)
       ->setMaxPageLength(100)
       ->withDeletedRestriction()
       ->withHiddenRestriction();

.. list-table:: GridDefinition methods
   :header-rows: 1
   :widths: 30 70

   * - Method
     - Purpose
   * - ``addColumn()``
     - Declare a column (name, label, searchable, orderable)
   * - ``setDefaultOrder()``
     - Fallback sort when the client sends no order
   * - ``setPageLength()``
     - Default page size hint (client may override up to the server limit)
   * - ``setMaxPageLength()``
     - Maximum rows returned by one request (100 by default)
   * - ``withDeletedRestriction()``
     - Adds ``deleted = 0`` (table must have ``deleted`` column)
   * - ``withHiddenRestriction()``
     - Adds ``hidden = 0`` (table must have ``hidden`` column)

Security defaults
=================

- Column names from the client are validated against the grid allowlist.
- All query values use Doctrine named parameters.
- The AJAX controller requires an authenticated backend user.
- Each grid must authorize the backend user in ``GridInterface::isAccessible()``.
- Search input, pagination, columns, and order clauses have server-side limits.
