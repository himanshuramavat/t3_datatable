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

|extension_name| has **no global extension configuration** (no ``ext_conf_template.txt``).
All behaviour is defined per grid via :php:`GridInterface::build()`.

Service registration
======================

The extension registers:

- :file:`Configuration/Services.yaml` — autowiring and ``t3datatable.grid`` tag
- :file:`Configuration/Backend/AjaxRoutes.php` — central data endpoint
- :file:`Configuration/JavaScriptModules.php` — ES module import map
- :file:`Configuration/Backend/Modules.php` — demo backend module only

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
     - Default page size hint (client may override via ``length``)
   * - ``withDeletedRestriction()``
     - Adds ``deleted = 0`` (table must have ``deleted`` column)
   * - ``withHiddenRestriction()``
     - Adds ``hidden = 0`` (table must have ``hidden`` column)

Security defaults
=================

- Column names from the client are validated against the grid allowlist.
- All query values use Doctrine named parameters.
- The AJAX controller requires an authenticated backend user.
