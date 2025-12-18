# Table MVC Project - Modular Structure

## პროექტის სტრუქტურა

```
table-mvc.loc/
├── app/
│   └── Table/                     # Table module (single home)
│       ├── Table.php              # Main Table class
│       ├── TableController.php    # API controller for table data
│       ├── TableModel.php         # Database model
│       ├── TableHelper.php        # Helper utilities
│       ├── views/
│       │   └── table.php          # HTML template
│       ├── assets/
│       │   ├── css/
│       │   │   └── table.css      # Styles
│       │   └── js/
│       │       └── table.js       # JavaScript
│       └── examples/
│           └── ExtendedTables.php  # Examples
├── doc/
│   ├── CHANGELOG.md               # Changelog
│   ├── PROJECT_STRUCTURE.md       # Project architecture (this file)
│   └── Table/
│       ├── README.md              # Module documentation
│       └── QUICK_START.md         # Quick start guide
├── config/
│   ├── db.php                     # Database configuration
│   ├── table_customers.php        # Table configuration
│   └── table_users.php            # Table configuration
├── public/
│   ├── index.php                  # Main entry point
│   ├── api/
│   │   └── table.php              # API endpoint
│   └── Table/
│       └── assets/
│           ├── css/table.css      # Published styles
│           └── js/table.js        # Published JavaScript
└── .osp/
    └── project.ini                # OSPanel configuration
```

## მოდულის გამოყენება

### 1. Table Instance-ის შექმნა

```php
// Load Table module
require_once __DIR__ . '/../app/Table/Table.php';

// Load configuration
$config = include __DIR__ . '/../config/table_customers.php';

// Create instance
$table = new Table('customers', $config);
```

### 2. Fluent API - Chaining

```php
$table = new Table('customers', $config);

$table->addField('new_column', 'New Column Label')
      ->addSortable('new_column')
      ->setApiUrl('/api/custom-endpoint')
      ->setConfig('searchPlaceholder', 'მოძებნე...');
```

### 3. Rendering

```php
<!DOCTYPE html>
<html>
<head>
    <?php echo $table->includeCss(); ?>
</head>
<body>
    <?php echo $table->render(); ?>
    <?php echo $table->includeJs(); ?>
</body>
</html>
```

## კონფიგურაციის მაგალითი

```php
// config/table_customers.php
return [
    'table' => 'customers',
    'fields' => [
        'id' => 'ID',
        'customer_name' => 'სახელი',
        'customer_email' => 'ელ. მისამართი',
        'customer_phone' => 'ტელეფონი',
        'created_at' => 'შექმნის თარიღი'
    ],
    'sortable' => ['id', 'customer_name', 'created_at'],
    'perPageOptions' => [5, 10, 20, 50],
    'defaultPerPage' => 10,
    'searchPlaceholder' => 'ძიება',
    'columnsButtonText' => 'სვეტები ▾',
    'apiUrl' => '/api/table'
];
```

## TableHelper გამოყენება

```php
require_once __DIR__ . '/../app/Table/TableHelper.php';

// Validate config
$errors = TableHelper::validateConfig($config);
if (!empty($errors)) {
    die('Configuration errors: ' . implode(', ', $errors));
}

// Sanitize input
$tableName = TableHelper::sanitizeTableName($_GET['table']);

// Build SQL clauses
$params = [];
$whereClause = TableHelper::buildSearchWhere($columns, $searchTerm, $params);
$orderClause = TableHelper::buildOrderBy($sortField, $sortDir, $sortableColumns);

// Calculate pagination
$pagination = TableHelper::calculatePagination($totalRecords, $page, $perPage);
```

## API Endpoint

```php
// public/api/table.php
<?php
require_once __DIR__ . '/../../app/Table/TableController.php';

$controller = new TableController();
$controller->fetch();
```

### API Request Format

```
GET /api/table?table=customers&page=1&per_page=10&search=john&sort_field=name&sort_dir=ASC
```

### API Response Format

```json
{
    "success": true,
    "headers": {
        "id": "ID",
        "customer_name": "სახელი"
    },
    "data": [
        {"id": 1, "customer_name": "John Doe"},
        {"id": 2, "customer_name": "Jane Smith"}
    ],
    "total_records": 100,
    "total_pages": 10
}
```

## ახალი Table-ის დამატება

### 1. შექმენით კონფიგურაცია

```php
// config/table_products.php
return [
    'table' => 'products',
    'fields' => [
        'id' => 'ID',
        'product_name' => 'პროდუქტი',
        'price' => 'ფასი'
    ],
    'sortable' => ['id', 'product_name', 'price']
];
```

### 2. გამოიყენეთ index.php-ში

```php
// Load config
$productsConfig = include __DIR__ . '/../config/table_products.php';

// Create instance
$productsTable = new Table('products', $productsConfig);

// Render
echo $productsTable->render();
```

### 3. განაახლეთ TableController

TableController უნდა იყენებდეს dynamic config loading based on table name.

## Customization

### სტილის შეცვლა

```css
/* app/Table/assets/css/table.css */
.mvc-table thead th {
    background: #your-brand-color;
}
```

### JavaScript Events

```javascript
// app/Table/assets/js/table.js
// დაამატეთ custom event listeners:

document.addEventListener('tableLoaded', function(e) {
    console.log('Table loaded:', e.detail);
});
```

### Template Customization

```php
<!-- app/Table/views/table.php -->
<!-- შეცვალეთ HTML სტრუქტურა თქვენი საჭიროებისამებრ -->
```

## Best Practices

1. **კონფიგურაციის ვალიდაცია** - გამოიყენეთ `TableHelper::validateConfig()`
2. **SQL Injection Protection** - გამოიყენეთ `TableHelper::sanitize*()` methods
3. **Error Handling** - დაამატეთ try-catch blocks
4. **Caching** - გამოიყენეთ localStorage browser-ში
5. **Performance** - ლიმიტირებული per-page options

## Migration from Old Structure

თუ გაქვთ ძველი სტრუქტურა:

### Before
```php
<?php require_once __DIR__ . '/../app/views/customers_view.php'; ?>
<script src="js/script.js"></script>
```

### After
```php
<?php 
$table = new Table('customers', $config);
echo $table->render();
echo $table->includeJs();
?>
```

## License

MIT
