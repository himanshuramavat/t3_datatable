# Contributing to T3 DataTable

Thanks for taking the time to contribute. Bug reports, ideas, documentation
fixes, and pull requests are all welcome.

This guide explains how to get set up, what the project expects before a change
is merged, and how to open a good pull request.

## Ways to help

- Report a bug or ask a question by opening a GitHub issue.
- Suggest a feature or an improvement.
- Improve the documentation in the `Documentation/` folder or the `README.md`.
- Fix a bug or add a feature with a pull request.

If you are planning a larger change, please open an issue first so we can agree
on the approach before you spend time on it.

## Requirements

To work on the extension locally you need:

- PHP 8.2, 8.3, or 8.4
- Composer
- Docker (only if you want to preview the documentation locally)

The extension supports TYPO3 13.4 and 14.3.

## Getting started

Fork the repository, clone your fork, and install the dependencies from the
package root:

```bash
composer install
```

This installs everything into the `.Build/` directory, including PHPUnit,
PHPStan, and PHP-CS-Fixer.

## Running the checks

Please run the same checks that CI runs before you push. All of them are
available as Composer scripts.

Unit and functional tests:

```bash
composer test            # unit tests
composer test:functional # functional tests
composer test:all        # both
```

Static analysis with PHPStan:

```bash
composer stan
```

Coding standards with PHP-CS-Fixer:

```bash
composer cs:check        # report problems only (dry run)
composer cs:fix          # apply the fixes for you
```

The CI pipeline also runs a plain `php -l` lint over the `Classes`,
`Configuration`, and `Tests` folders. If your code parses and the commands
above pass, CI should pass too.

## Coding standards

- Keep `declare(strict_types=1);` at the top of every PHP file.
- Follow the existing style. PHP-CS-Fixer settled most of it, so run
  `composer cs:fix` before committing.
- Add or update tests when you change behaviour.
- Keep public methods and classes documented where the type hints are not
  enough on their own.

## Adding or changing a grid feature

Most changes touch the small set of classes under `Classes/`:

- `Contract/GridInterface.php` is the contract every grid implements.
- `DataTable/GridDefinition.php` and `DataTable/ColumnDefinition.php` describe
  a grid and its columns.
- `Engine/QueryEngine.php` builds and runs the Doctrine DBAL query.
- `Security/ColumnAllowlist.php` checks that a request only touches columns the
  grid declared.
- `Request/DataTableRequest.php` and `Response/DataTableResponse.php` map the
  DataTables protocol to and from PHP.

If you add a new option to `GridDefinition`, please cover it with a test and
mention it in `Documentation/Configuration/Index.rst`.

## Commit messages

Write short, clear commit messages that say what changed and why. One logical
change per commit makes review easier.

## Pull requests

1. Create a branch off `main` for your work.
2. Make your change and run the checks above.
3. Push the branch to your fork.
4. Open a pull request against `main` and describe what you changed and why.
5. Link any related issue.

A maintainer will review the pull request. Please be ready to make small
adjustments if something is requested.

## Reporting security issues

Please do not report security problems in public issues. Contact the author
privately instead, so the issue can be fixed before it is disclosed. Author
details are in the `README.md` and `composer.json`.

## License

By contributing, you agree that your work is licensed under the
[GNU General Public License v2.0 or later](https://www.gnu.org/licenses/gpl-2.0.html),
the same license as the rest of the extension.
