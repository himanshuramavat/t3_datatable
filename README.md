# T3 DataTable for TYPO3

### Server-side searchable, sortable & paginated data grids for TYPO3 backend modules

<p align="center">
  <img src="https://img.shields.io/badge/Version-1.0.0-blue" alt="Version 1.0.0">
  <img src="https://img.shields.io/badge/license-GPL--2.0--or--later-blue.svg" alt="License">
  <img src="https://img.shields.io/badge/TYPO3-13,14-orange" alt="TYPO3 13-14">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-red" alt="PHP 8.2+">
</p>

---

## ✨ What is T3 DataTable?

**T3 DataTable** brings server-side **searchable, sortable, and paginated** data grids to TYPO3 backend modules — without writing custom AJAX endpoints, SQL boilerplate, or inline JavaScript.

Register a grid, load the ES module, and render a table with `data-*` attributes. The extension handles request parsing, Doctrine DBAL queries, column allowlisting, and JSON responses compatible with the [DataTables server-side protocol](https://datatables.net/manual/server-side).

---

## 🚀 Key Features

- 🔍 Server-side search, sort, and pagination over any database table
- 🧩 Simple grid API — implement `GridInterface`, tag it, done
- 🛡️ Column allowlisting before any search or sort touches the query
- 🔒 CSP-safe JavaScript bootstrap (no inline scripts)
- 🧱 TYPO3-native backend module shell (`ModuleTemplate`, docheader, infobox, card UI)
- 🔌 One shared AJAX endpoint (`t3datatable_data`) for all grids
- ✅ PHPUnit, PHPStan, and PHP-CS-Fixer wired in
- 📚 Standard TYPO3 documentation in `Documentation/`

---

## 📦 Installation

### ➤ Composer (Packagist)

```bash
composer require hrr/t3-datatable
```

🔗 https://packagist.org/users/himanshuramavat/

Then open **Admin Tools → T3 DataTable → Demo grid** to see the built-in `pages` demo.

---

## 🧠 Quick Usage Example

**1. Define a grid:**

```php
use HRR\T3Datatable\Contract\GridInterface;
use HRR\T3Datatable\DataTable\GridDefinition;

final class PagesGrid implements GridInterface
{
    public function getIdentifier(): string
    {
        return 'demo_pages';
    }

    public function getTableName(): string
    {
        return 'pages';
    }

    public function build(GridDefinition $definition): void
    {
        $definition
            ->addColumn('uid', 'UID', searchable: false, orderable: true)
            ->addColumn('title', 'Title', searchable: true, orderable: true)
            ->addColumn('slug', 'Slug', searchable: true, orderable: true)
            ->setDefaultOrder('uid', 'ASC')
            ->setPageLength(10);
    }
}
```

**2. Tag it in your `Configuration/Services.yaml`:**

```yaml
Vendor\MyExt\Grid\PagesGrid:
  tags: ['t3datatable.grid']
```

**3. Render the table (no inline JS — CSP-safe):**

```php
// In your backend controller
$this->pageRenderer->loadJavaScriptModule('@hrr/t3-datatable/datatable-backend.js');
```

```html
<table data-t3-datatable="demo_pages"
       data-columns="{columnsJson}"
       data-page-length="10">
    <thead>…</thead>
    <tbody></tbody>
</table>
```

The module auto-initializes every `[data-t3-datatable]` table on `DocumentService.ready()`.

---

## 🧰 Compatibility

| TYPO3       | PHP       | Extension Version |
| ----------- | --------- | ----------------- |
| 13.4 – 14.3 | 8.2 – 8.4 | 1.0.0             |

---

## 🛠️ Development

From the package root:

```bash
composer install
composer test:all      # unit + functional
composer stan          # PHPStan
composer cs:check      # PHP-CS-Fixer (dry-run)
```

---

## 🙏 Credits & Acknowledgements

This extension was inspired by and built with help from the following projects and developers:

| Project / author | Role in `t3_datatable` |
|------------------|------------------------|
| **[yajra/laravel-datatables](https://github.com/yajra/laravel-datatables)** by [Arjay Angeles (yajra)](https://github.com/yajra) | Server-side DataTables protocol, grid engine concept, and query-processing patterns adapted for TYPO3 and Doctrine DBAL |

---

## 👨‍💻 Author

- **[Himanshu Ramavat](https://www.linkedin.com/in/himanshu-ramavat/)** — [himanshuramavat.in](https://himanshuramavat.in) · [Packagist](https://packagist.org/users/himanshuramavat/)

---

## 💡 Contributing

Contributions are welcome and appreciated ❤️

- Fork the repository
- Create a feature branch
- Commit your changes
- Submit a Pull Request

---

## 📜 License

Licensed under the [GNU General Public License v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html).

---

<p align="center">
  <b>Made with 🧡 for the TYPO3 Developer</b>
</p>
