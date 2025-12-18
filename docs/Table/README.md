# Table Module

Reusable table component with AJAX, sorting, pagination, search and column visibility control.

## მახასიათებლები

- ✅ AJAX მონაცემების ჩატვირთვა
- ✅ Sorting (ასვლა/დაღვლა)
- ✅ Pagination
- ✅ Search (ძიება)
- ✅ Column visibility control
- ✅ Responsive design
- ✅ localStorage - შენახული პარამეტრები
- ✅ Bootstrap 5 თავსებადი

## სტრუქტურა

```
app/Table/
├── Table.php            # Main Table class
├── TableController.php  # API controller
├── TableModel.php       # Database model
├── TableHelper.php      # Helper utilities
├── views/
│   └── table.php        # Table HTML template
└── assets/
    ├── css/
    │   └── table.css    # Table styles (copied to public/Table/assets)
    └── js/
        └── table.js     # Table JavaScript (copied to public/Table/assets)
```

## გამოყენება

### 1. კონფიგურაცია

შექმენით კონფიგურაციის ფაილი (მაგ: `config/table_customers.php`):

```php
<?php
return [
    'table' => 'customers',
    'fields' => [
        'id' => 'ID',
        'customer_name' => 'სახელი',
        'customer_email' => 'ელ. მისამართი',
    ],
    'sortable' => ['id', 'customer_name'],
    'perPageOptions' => [5, 10, 20, 50],
    'defaultPerPage' => 10,
    'searchPlaceholder' => 'ძიება',
    'columnsButtonText' => 'სვეტები ▾',
    'apiUrl' => '/api/table'
];
```

### 2. ინიციალიზაცია

```php
// Load module
require_once __DIR__ . '/../app/Table/Table.php';

// Load config
$config = include __DIR__ . '/../config/table_customers.php';

// Create table instance
$table = new Table('customers', $config);
```

### 3. HTML-ში ჩართვა

```php
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <?php echo $table->includeCss(); ?>
</head>
<body>
    <div class="container">
        <h1>ცხრილი</h1>
        <?php echo $table->render(); ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $table->includeJs(); ?>
</body>
</html>
```

### 4. API Endpoint

Table მოდულს სჭირდება API endpoint მონაცემების მისაღებად:

```php
// public/api/table.php
<?php
require_once __DIR__ . '/../../app/Table/TableController.php';

$controller = new TableController();
$controller->fetch();
```

## Table კლასის მეთოდები

### `__construct($tableId, $config = [])`
ინიციალიზაცია table ID-თი და კონფიგურაციით.

### `render()`
გენერირებს table HTML-ს კონტროლებით (search, pagination, etc).

### `includeCss()`
აბრუნებს CSS ფაილის include tag-ს.

### `includeJs()`
აბრუნებს JavaScript ფაილის include tag-ს და კონფიგურაციას.

### `getConfigJson()`
აბრუნებს კონფიგურაციას JSON ფორმატში.

### `getTableId()`
აბრუნებს table ID-ს.

### `getConfig($key, $default = null)`
აბრუნებს კონფიგურაციის მნიშვნელობას.

## კონფიგურაციის პარამეტრები

| პარამეტრი | ტიპი | აღწერა |
|-----------|------|--------|
| `table` | string | ბაზის ცხრილის სახელი |
| `fields` | array | სვეტები (key => label) |
| `sortable` | array | sortable სვეტების სია |
| `perPageOptions` | array | per-page options |
| `defaultPerPage` | int | default per-page value |
| `searchPlaceholder` | string | search input placeholder |
| `columnsButtonText` | string | columns button text |
| `apiUrl` | string | API endpoint URL |

## Customization

### სტილის შეცვლა

რედაქტირება `app/Table/assets/css/table.css`:

```css
.mvc-table thead th {
    background: #your-color;
}
```

### JavaScript ფუნქციონალის დამატება

რედაქტირება `app/Table/assets/js/table.js` - დაამატეთ custom event listeners ან functions.

### Template-ის შეცვლა

რედაქტირება `app/Table/views/table.php` - შეცვალეთ HTML სტრუქტურა.

## მაგალითები

### მარტივი ცხრილი

```php
$table = new Table('users', [
    'table' => 'users',
    'fields' => ['id' => 'ID', 'name' => 'Name', 'email' => 'Email'],
    'sortable' => ['id', 'name']
]);

echo $table->render();
```

### მრავალი ცხრილი ერთ გვერდზე

```php
// Table 1
$customers = new Table('customers', $customersConfig);
echo $customers->render();

// Table 2  
$products = new Table('products', $productsConfig);
echo $products->render();
```

## License

MIT
