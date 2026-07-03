# T3 DataTable for TYPO3

### Server-side searchable, sortable & paginated data grids for TYPO3 backend modules

<p align="center">
  <img src="https://img.shields.io/badge/Version-1.0.0-blue" alt="Version 1.0.0">
  <img src="https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg" alt="License">
  <img src="https://img.shields.io/badge/TYPO3-13,14-orange" alt="TYPO3 13-14">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-red" alt="PHP 8.2+">
</p>

---

## What is T3 DataTable?

**T3 DataTable** brings server-side **searchable, sortable, and paginated** data grids to TYPO3 backend modules, without writing custom AJAX endpoints, SQL boilerplate, or inline JavaScript.

Register a grid, load the ES module, and render a table with `data-*` attributes. The extension handles request parsing, Doctrine DBAL queries, column allowlisting, and JSON responses compatible with the [DataTables server-side protocol](https://datatables.net/manual/server-side).

---

## Key Features

→ Server-side search, sort, and pagination over any database table  
→ Simple grid API: implement `GridInterface`, tag it, done  
→ Column allowlisting before any search or sort touches the query  
→ CSP-safe JavaScript bootstrap (no inline scripts)  
→ TYPO3-native backend module shell (`ModuleTemplate`, docheader, infobox, card UI)  
→ One shared AJAX endpoint (`t3datatable_data`) for all grids  
→ PHPUnit, PHPStan, and PHP-CS-Fixer wired in  
→ Standard TYPO3 documentation in `Documentation/`

---

## Installation

### Composer (Packagist)

```bash
composer require hrr/t3-datatable
```

Packagist: https://packagist.org/users/himanshuramavat/

Then open **Admin Tools → T3 DataTable → Demo grid** to see the built-in `pages` demo.

---

## Documentation

Full documentation is available online, including setup instructions, grid
configuration, and a step-by-step integration guide for adding a grid to your
own extension:

**https://docs.typo3.org/p/hrr/t3-datatable/main/en-us/**

---

## Compatibility

| TYPO3       | PHP       | Extension Version |
| ----------- | --------- | ----------------- |
| 13.4 - 14.3 | 8.2 - 8.4 | 1.0.0             |

---

## Development

From the package root:

```bash
composer install
composer test:all      # unit + functional
composer stan          # PHPStan
composer cs:check      # PHP-CS-Fixer (dry-run)
```

---

## Credits & Acknowledgements

This extension was inspired by and built with help from the following projects and developers:

| Project / author | Role in `t3_datatable` |
|------------------|------------------------|
| **[yajra/laravel-datatables](https://github.com/yajra/laravel-datatables)** by [Arjay Angeles (yajra)](https://github.com/yajra) | Server-side DataTables protocol, grid engine concept, and query-processing patterns adapted for TYPO3 and Doctrine DBAL |

---

## Authors

→ **[Himanshu Ramavat](https://www.linkedin.com/in/himanshu-ramavat/)**  
→ **[Rohan Parmar](https://www.linkedin.com/in/rohanrparmar)**

---

## Contributing

Contributions are welcome and appreciated. Please read the
[Contributing Guide](CONTRIBUTING.md) before opening a pull request.

---

## License

Licensed under the [GNU General Public License v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

<p align="center">
  <b>Made with 🧡 for the TYPO3 Developer</b>
</p>
