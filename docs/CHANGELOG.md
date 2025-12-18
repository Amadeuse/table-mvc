# Changelog

All notable changes to the Table Module project will be documented in this file.

## [2.0.0] - 2025-12-18

### ✨ Major Refactoring - Modular Architecture

#### Added
- **Table Module** - Standalone reusable component
  - `app/Table/Table.php` - Main Table class
  - `app/Table/TableHelper.php` - Utility functions
  - `app/Table/views/table.php` - HTML template
  - `app/Table/assets/css/table.css` - Styles
  - `app/Table/assets/js/table.js` - JavaScript

- **Fluent API** - Chainable methods
  - `addField()` - Add column
  - `removeField()` - Remove column
  - `addSortable()` - Add sortable column
  - `removeSortable()` - Remove sortable column
  - `setConfig()` - Set configuration value
  - `setApiUrl()` - Set API endpoint

- **TableHelper Class** - Utility functions
  - `validateConfig()` - Validate configuration
  - `sanitizeTableName()` - SQL injection protection
  - `sanitizeColumnName()` - SQL injection protection
  - `buildSearchWhere()` - Build WHERE clause
  - `buildOrderBy()` - Build ORDER BY clause
  - `calculatePagination()` - Pagination calculations
  - `formatValue()` - Format field values
  - `exportConfig()` - Export to JSON
  - `importConfig()` - Import from JSON

- **Examples** - Extended functionality examples
  - `TableWithExport` - CSV export
  - `TableWithEditing` - Inline editing
  - `TableWithFilters` - Filter controls

- **Documentation**
  - `README.md` - Module documentation
  - `PROJECT_STRUCTURE.md` - Project architecture
  - `examples/ExtendedTables.php` - Usage examples

#### Changed
- **Unique CSS Classes** - All classes prefixed with `mvc-`
  - `.table` → `.mvc-table`
  - `.pagination` → `.mvc-pagination`
  - `.controls` → `.mvc-controls`
  - Bootstrap 5 compatible

- **Generic IDs** - Removed specific names (e.g., "customers")
  - `search_customers` → `mvc_table_search`
  - `table_customers` → `mvc_table`
  - `pagination_customers` → `mvc_table_pagination`

- **Responsive Design** - Enhanced mobile support
  - Horizontal scroll on mobile
  - Adaptive font sizes
  - Compact pagination
  - Touch-friendly controls

- **Pagination Styling** - Improved appearance
  - Smaller buttons
  - Better spacing
  - Cleaner borders

#### Improved
- **Code Organization** - Modular structure
- **Reusability** - Easy to use in multiple projects
- **Maintainability** - Separated concerns
- **Extensibility** - Easy to add new features
- **Documentation** - Comprehensive guides

## [1.0.0] - Initial Version

### Added
- Basic table with AJAX
- Sorting functionality
- Pagination
- Search
- Column visibility control
- localStorage for settings
- Bootstrap integration

### Features
- Dynamic data loading
- Sortable columns
- Per-page selection
- Search across all columns
- Show/hide columns
- Responsive layout

---

## Migration Guide: 1.0 → 2.0

### Old Code (v1.0)
```php
<?php require_once __DIR__ . '/../app/views/customers_view.php'; ?>
<link rel="stylesheet" href="css/table.css">
<script src="js/script.js"></script>
```

### New Code (v2.0)
```php
<?php
require_once __DIR__ . '/../app/Table/Table.php';
$config = include __DIR__ . '/../config/table_customers.php';
$table = new Table('customers', $config);
?>

<?php echo $table->includeCss(); ?>
<?php echo $table->render(); ?>
<?php echo $table->includeJs(); ?>
```

### Benefits
- ✅ Cleaner code
- ✅ Reusable across projects
- ✅ Easy to customize
- ✅ Better organization
- ✅ No naming conflicts
- ✅ Bootstrap 5 ready
