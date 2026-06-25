.. include:: /Includes.rst.txt

.. _installation:

============
Installation
============

.. contents::
   :local:
   :depth: 2

Requirements
============

- TYPO3 13.4 or 14.3
- PHP 8.2–8.5

Install with Composer
=====================

.. code-block:: bash
   :caption: Require the package

   composer require hrr/t3-datatable

For local path repositories (monorepo):

.. code-block:: bash

   composer require hrr/t3-datatable:@dev

Activate extension
==================

1. Open the TYPO3 backend.
2. Go to :guilabel:`Admin Tools > Extensions`.
3. Activate :t3ext:`t3_datatable`.

Verify installation
===================

After activation:

- AJAX route ``t3datatable_data`` is registered.
- JavaScript import map entry ``@hrr/t3-datatable/datatable-backend.js`` is available.
- Demo module appears under :guilabel:`Admin Tools > T3 DataTable > Demo grid` (admin only).

Run tests (optional)
====================

From the package directory:

.. code-block:: bash

   composer install
   composer test
   composer test:functional
   composer stan
