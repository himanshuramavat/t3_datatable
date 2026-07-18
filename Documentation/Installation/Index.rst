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

- TYPO3 13 LTS or 14
- PHP 8.2 or newer

Install with Composer
=====================

.. code-block:: bash
   :caption: Require the package

   composer require hrr/t3-datatable

Packagist: https://packagist.org/packages/hrr/t3-datatable

For local path repositories (monorepo):

.. code-block:: bash

   composer require hrr/t3-datatable:@dev

Install from TER
================

You can also install the extension from the
`TYPO3 Extension Repository <https://extensions.typo3.org/extension/t3_datatable/>`_
using the Extension Manager, or download it and place it in your project.

Activate extension
==================

1. Open the TYPO3 backend.
2. Go to :guilabel:`Admin Tools > Extensions`.
3. Activate :t3ext:`t3_datatable`.

Demo
====

After activation, open:

:guilabel:`Web > T3 DataTable`

to see a working server-side grid using the ``pages`` table.

.. note::

   On TYPO3 14 the module appears under
   :guilabel:`Content > T3 DataTable`.

Verify installation
===================

After activation:

- AJAX route ``t3datatable_data`` is registered.
- JavaScript import map entry ``@hrr/t3-datatable/datatable-backend.js`` is available.
- Demo module appears under :guilabel:`Web > T3 DataTable`
  (:guilabel:`Content > T3 DataTable` on TYPO3 14).

Run tests (optional)
====================

From the package directory:

.. code-block:: bash

   composer install
   composer ci
