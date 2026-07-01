.. include:: /Includes.rst.txt

====================
Registering a grid
====================

.. contents::
   :local:
   :depth: 2

Step 1: Implement GridInterface
================================

.. code-block:: php
   :caption: Classes/DataTable/MyRecordsGrid.php

   <?php

   declare(strict_types=1);

   namespace MyVendor\MyExt\DataTable;

   use HRR\T3Datatable\Contract\GridInterface;
   use HRR\T3Datatable\DataTable\GridDefinition;

   final class MyRecordsGrid implements GridInterface
   {
       public function getIdentifier(): string
       {
           return 'myext_records';
       }

       public function getTableName(): string
       {
           return 'tx_myext_records';
       }

       public function build(GridDefinition $definition): void
       {
           $definition
               ->addColumn('uid', 'UID', searchable: false, orderable: true)
               ->addColumn('title', 'Title', searchable: true, orderable: true)
               ->addColumn('crdate', 'Created', searchable: false, orderable: true)
               ->setDefaultOrder('crdate', 'DESC')
               ->setPageLength(25)
               ->withDeletedRestriction()
               ->withHiddenRestriction();
       }
   }

Step 2: Tag in Services.yaml
=============================

.. code-block:: yaml
   :caption: Configuration/Services.yaml

   services:
     _instanceof:
       HRR\T3Datatable\Contract\GridInterface:
         tags: ['t3datatable.grid']

     MyVendor\MyExt\:
       resource: '../Classes/*'

The grid class is auto-registered when it implements :php:`GridInterface`.

Step 3: Use the grid identifier in JavaScript
==============================================

Pass the same identifier to :js:`initDataTable()`:

.. code-block:: javascript

   initDataTable('#my-table', {
     gridIdentifier: 'myext_records',
     columns: [
       { data: 'uid', title: 'UID' },
       { data: 'title', title: 'Title' },
       { data: 'crdate', title: 'Created' },
     ],
   });

Column rules
============

- Only columns declared in :php:`build()` can be searched or sorted.
- Request column names must match the allowlist regex ``^[a-zA-Z0-9_.]+$``.
- Use ``withDeletedRestriction()`` / ``withHiddenRestriction()`` only when the
  target table has those fields.

Demo reference
==============

See :php:`HRR\T3Datatable\Demo\PagesGrid` in this extension for a working
``pages`` example.
