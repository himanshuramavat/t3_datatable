.. include:: /Includes.rst.txt

.. _start:

=============
T3 DataTable
=============

:Extension key:
   t3_datatable

:Package name:
   hrr/t3-datatable

:Language:
   en

:Author:
   Himanshu Ramavat, Rohan Parmar

:License:
   GPL-2.0-or-later

:Repository:
   https://github.com/himanshuramavat/t3_datatable

:Issues:
   https://github.com/himanshuramavat/t3_datatable/issues

----

T3 DataTable is a server-side grid engine for **custom TYPO3 backend modules**.
Register a grid for any database table and you get a secured AJAX endpoint plus
a searchable, sortable, and paginated table, without writing SQL boilerplate by
hand. It complements (and does not replace) the core :guilabel:`Web > List`
module (:php:`DatabaseRecordList`) used for TCA records on a page.

This documentation is for two groups of people:

- Extension developers who want to add DataTable grids to their backend
  modules.
- Integrators who just need to install and switch on the package.

----

.. card-grid::
   :columns: 1
   :columns-md: 2
   :gap: 4
   :card-height: 100

   .. card:: Introduction

      What the extension does and how it fits into TYPO3.

      .. card-footer:: :ref:`Read more <introduction>`
         :button-style: btn btn-primary stretched-link

   .. card:: Installation

      Install and activate the extension.

      .. card-footer:: :ref:`Read more <installation>`
         :button-style: btn btn-primary stretched-link

   .. card:: Developer guide

      Register grids, wire DI, and use the JavaScript module.

      .. card-footer:: :ref:`Read more <developer>`
         :button-style: btn btn-secondary stretched-link

   .. card:: Usage

      Demo backend module and AJAX endpoint.

      .. card-footer:: :ref:`Read more <usage>`
         :button-style: btn btn-secondary stretched-link

----

**Table of contents**

.. toctree::
   :maxdepth: 2
   :titlesonly:

   Introduction/Index
   Installation/Index
   Configuration/Index
   Developer/Index
   Usage/Index
   Troubleshooting/Index

.. toctree::
   :hidden:

   Sitemap
