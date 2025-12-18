# Table MVC - Composer პაკეტი

AJAX-იანი რეაქტიული ცხრილი PHP პროექტებისთვის.

## მახასიათებლები

- ✅ AJAX მონაცემის ჩატვირთვა
- ✅ Sorting (აღმავალი/დაღმავალი)
- ✅ Pagination კონტროლი
- ✅ ძიება (search)
- ✅ სვეტების ხილვადობის კონტროლი
- ✅ Responsive დიზაინი
- ✅ localStorage - პარამეტრების შენახვა
- ✅ Bootstrap 4/5 თავსებადი
- ✅ ქართული ენის მხარდაჭერა

## ინსტალაცია Composer-ით

```bash
composer require yourname/table-mvc
```

## სწრაფი დაწყება

### 1. კლასების იმპორტი

```php
use TableMvc\Table;
use TableMvc\TableModel;
use TableMvc\TableHelper;
```

### 2. კონფიგურაციის შექმნა

```php
$config = [
    'table' => 'users',
    'fields' => [
        'id' => 'ID',
        'name' => 'სახელი',
        'email' => 'ელ. მისამართი'
    ],
    'sortable' => ['id', 'name'],
    'apiUrl' => '/api/table'
];
```

### 3. Table ინსტანსის შექმნა

```php
define('BASE_URL', '/');
$table = new Table('users', $config);
```

### 4. HTML-ში ჩართვა

```html
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <?php echo $table->includeCss(); ?>
</head>
<body>
    <div class="container mt-4">
        <h1>მომხმარებლები</h1>
        <?php echo $table->render(); ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $table->includeJs(); ?>
</body>
</html>
```

## Table კლასის მეთოდები

### `__construct($tableId, $config = [])`
ინიციალიზაციონი table ID-თი და კონფიგურაციით.

### `render($tableName = null)`
ცხრილის HTML-ის გენერირება.

### `includeCss()`
CSS ფაილის include tag-ი.

### `includeJs()`
JS ფაილის include tag და აპლიკაციის კონფიგურაცია.

### Fluent API

```php
$table->addField('new_col', 'ახალი სვეტი')
      ->addSortable('new_col')
      ->setApiUrl('/api/custom')
      ->setConfig('searchPlaceholder', 'მოძებნე...');
```

## TableModel - ბაზის გამოწვევა

```php
$pdo = new PDO('mysql:host=localhost;dbname=mydb', 'user', 'pass');
$model = new TableModel($pdo, 'users', ['id', 'name', 'email'], ['id', 'name']);

$result = $model->getData(
    page: 1,
    search: '',
    sortField: 'name',
    sortDir: 'ASC'
);
```

## TableHelper - დამხმარე ფუნქციები

```php
// კონფიგურაციის ვალიდაცია
$errors = TableHelper::validateConfig($config);

// Sanitize
$safeName = TableHelper::sanitizeTableName($_GET['table']);

// მნიშვნელობის ფორმატირება
echo TableHelper::formatValue($date, 'date');
echo TableHelper::formatValue($price, 'currency');

// Pagination
$pagination = TableHelper::calculatePagination($total, $page, $perPage);
```

## API Endpoint

Table მოდულს მოსახლეობის განახლებული მონაცემისთვის სჭირდება API endpoint.

მაგალითი `public/api/table.php`:

```php
<?php
use TableMvc\TableController;

$pdo = new PDO(...);
$controller = new TableController($pdo);
$controller->fetch();
```

## კასტომიზაცია

### CSS

პაკეტი მოცემულ `assets/css/table.css` ფაილს აშავებს. თქვენი `BASE_URL` სწორად დაყენებული უნდა იყოს.

### JS

`assets/js/table.js` თქვენს დაკონფიგურირებულ API endpoint-თან ეკომუნიკაციას ახდენს.

## მაგალითები

იხილეთ `examples/` დირექტორია გაფართოებული მაგალითებისთვის:
- `TableWithExport` - CSV ექსპორტი
- `TableWithEditing` - Inline რედაქტირება
- `TableWithFilters` - ფილტრების კონტროლი

## ლიცენზია

MIT

## მხარდაჭერა

დაკითხვებისთვის ან პრობლემებისთვის გთხოვთ უფროსი issue გახსნათ GitHub-ზე.
"# table-mvc" 
