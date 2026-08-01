[![Latest Stable Version](https://poser.pugx.org/hrr/t3-datatable/v/stable)](https://packagist.org/packages/hrr/t3-datatable)
[![TYPO3 13](https://img.shields.io/badge/TYPO3-13-orange.svg)](https://get.typo3.org/version/13)
[![TYPO3 14](https://img.shields.io/badge/TYPO3-14-orange.svg)](https://get.typo3.org/version/14)
[![CI](https://github.com/himanshuramavat/t3_datatable/actions/workflows/ci.yml/badge.svg)](https://github.com/himanshuramavat/t3_datatable/actions/workflows/ci.yml)
[![Crowdin](https://badges.crowdin.net/typo3-extension-t3datatable/localized.svg)](https://crowdin.com/project/typo3-extension-t3datatable)
# T3 DataTable

Server-side searchable, sortable and paginated data grids for TYPO3 backend modules.

**T3 DataTable** brings server-side data grids to custom TYPO3 backend modules
without writing custom AJAX endpoints, SQL boilerplate, or inline JavaScript.
It complements the core **Web → List** module (`DatabaseRecordList`); it does
not replace TCA record lists on a page.

Register a grid, load the ES module, and render a table with `data-*`
attributes. The extension handles request parsing, Doctrine DBAL queries,
column allowlisting, and JSON responses compatible with the
[DataTables server-side protocol](https://datatables.net/manual/server-side).

|                  | URL                                                              |
|------------------|------------------------------------------------------------------|
| **Repository:**  | https://github.com/himanshuramavat/t3_datatable                  |
| **Read online:** | https://docs.typo3.org/p/hrr/t3-datatable/main/en-us/            |
| **TER:**         | https://extensions.typo3.org/extension/t3_datatable/             |
| **Packagist:**   | https://packagist.org/packages/hrr/t3-datatable                  |
| **Issues:**      | https://github.com/himanshuramavat/t3_datatable/issues           |

## Features

* Server-side search, sort, and pagination over any database table
* Simple grid API: implement `GridInterface`, tag it, done
* Column allowlisting before any search or sort touches the query
* CSP-safe JavaScript bootstrap (no inline scripts)
* TYPO3-native backend module shell (`ModuleTemplate`, card UI)
* One shared AJAX endpoint (`t3datatable_data`) for all grids
* PHPUnit, PHPStan, and PHP-CS-Fixer wired in
* Standard TYPO3 documentation in `Documentation/`

## Installation

Install via Composer:

```bash
composer require hrr/t3-datatable
```

Or install the extension from the
[TYPO3 Extension Repository](https://extensions.typo3.org/extension/t3_datatable/).

Then activate **T3 DataTable** in **Admin Tools → Extensions**.

## Demo

After installation open

**Web → T3 DataTable**

to see a working server-side grid using the `pages` table.
(On TYPO3 14 the module appears under **Content → T3 DataTable**.)

## Quick start

1. Implement `GridInterface` in your extension and tag the service with
   `t3datatable.grid` (or rely on `_instanceof` auto-tagging).
2. Load the ES module in your backend controller:

   ```php
   $this->pageRenderer->loadJavaScriptModule('@hrr/t3-datatable/datatable-backend.js');
   ```

3. Render a table with `data-*` attributes (see the demo Fluid partial
   `Resources/Private/Partials/Module/DataTablePanel.html`), or call
   `initDataTable()` from JavaScript.

Full step-by-step instructions are in the
[developer documentation](https://docs.typo3.org/p/hrr/t3-datatable/main/en-us/Developer/Index.html).

## Documentation

https://docs.typo3.org/p/hrr/t3-datatable/main/en-us/

## Requirements

* TYPO3 13 LTS / 14
* PHP 8.2+

## Compatibility

| TYPO3       | PHP       | Extension Version |
| ----------- | --------- | ----------------- |
| 13.4 - 14.3 | 8.2 - 8.4 | 2.0.0             |

## Development

From the package root:

```bash
composer install
composer ci             # cs:check + stan + unit + functional
composer test:all       # unit + functional
composer stan           # PHPStan
composer cs:check       # PHP-CS-Fixer (dry-run)
```

## Contributing

Contributions are welcome. Please read the
[Contributing Guide](CONTRIBUTING.md) before opening a pull request.

## Acknowledgements

This extension was inspired by and built with help from the following projects
and developers:

| Project / author | Role in `t3_datatable` |
| ---------------- | ---------------------- |
| **[yajra/laravel-datatables](https://github.com/yajra/laravel-datatables)** by [Arjay Angeles (yajra)](https://github.com/yajra) | Server-side DataTables protocol, grid engine concept, and query-processing patterns adapted for TYPO3 and Doctrine DBAL |

## Authors

* [Himanshu Ramavat](https://www.linkedin.com/in/himanshu-ramavat/)
* [Rohan Parmar](https://www.linkedin.com/in/rohanrparmar)

## License

Licensed under the
[GNU General Public License v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html).
