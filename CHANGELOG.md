# Changelog

All notable changes to this project are documented in this file. This project
follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and Semantic
Versioning.

## [2.0.0] - 2026-08-01

### Breaking changes

- `GridInterface` now requires `isAccessible(BackendUserAuthentication $backendUser): bool`.
  Every grid must explicitly authorize its data before the shared AJAX endpoint
  can return it.
- Invalid, oversized, and unbounded DataTables requests are rejected. A request
  must use a positive `length`; the default server-side maximum is 100 rows and
  can be changed with `GridDefinition::setMaxPageLength()`.
- All valid DataTables order clauses are applied, in order. Search terms treat
  `%`, `_`, and `!` literally.

### Security

- Validate grid/table/column configuration, reject duplicate grid identifiers,
  and quote ordered identifiers.

## [1.0.0]

Initial public release.
