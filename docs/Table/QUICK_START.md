# Quick Start Guide - Table Module

## 🚀 5 წუთში დაწყება

### 1️⃣ კონფიგურაციის შექმნა

შექმენით `config/table_yourname.php`:

```php
<?php
return [
    'table' => 'your_table_name',
    'fields' => [
        'id' => 'ID',
        'name' => 'სახელი',
        'email' => 'Email'
    ],
    'sortable' => ['id', 'name'],
    'defaultPerPage' => 10
];
```

### 2️⃣ გამოყენება PHP-ში

```php
<?php
// Load module
require_once __DIR__ . '/../app/Table/Table.php';

// Load config
$config = include __DIR__ . '/../config/table_yourname.php';

// Create table
$table = new Table('yourname', $config);
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <?php echo $table->includeCss(); ?>
</head>
<body>
    <div class="container mt-4">
        <h1>My Table</h1>
        <?php echo $table->render(); ?>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $table->includeJs(); ?>
</body>
</html>
```

### 3️⃣ API Endpoint

გამოიყენეთ არსებული `public/api/table.php` ან შექმენით ახალი.

---

## 🎨 კასტომიზაცია

### სტილის შეცვლა

```php
$table->setConfig('searchPlaceholder', 'მოძებნე...')
      ->setConfig('columnsButtonText', 'Columns ▼');
```

### სვეტის დამატება

```php
$table->addField('new_column', 'New Label')
      ->addSortable('new_column');
```

### API URL-ის შეცვლა

```php
$table->setApiUrl('/api/custom-endpoint');
```

---

## 📱 მაგალითები

### მარტივი ცხრილი
```php
$table = new Table('users', [
    'table' => 'users',
    'fields' => ['id' => 'ID', 'name' => 'Name'],
    'sortable' => ['id']
]);
```

### რთული ცხრილი
```php
$table = new Table('products', [
    'table' => 'products',
    'fields' => [
        'id' => 'ID',
        'name' => 'პროდუქტი',
        'price' => 'ფასი',
        'stock' => 'მარაგი',
        'created_at' => 'თარიღი'
    ],
    'sortable' => ['id', 'name', 'price', 'created_at'],
    'perPageOptions' => [10, 25, 50, 100],
    'defaultPerPage' => 25
]);
```

---

## 🛠️ Troubleshooting

### ცხრილი არ ჩანს?
- შეამოწმეთ CSS ფაილი იტვირთება თუ არა
- შეამოწმეთ JavaScript კონსოლი errors-ზე

### მონაცემები არ იტვირთება?
- შეამოწმეთ API endpoint მუშაობს თუ არა
- გახსენით Browser DevTools → Network tab
- შეამოწმეთ TableController error logs

### სტილები არ მუშაობს?
- დარწმუნდით რომ Bootstrap 4/5 ჩატვირთულია
- შეამოწმეთ `mvc-` prefix კლასები სწორია თუ არა

---

## 📚 დამატებითი რესურსები

- [README.md](README.md) - სრული დოკუმენტაცია
- [PROJECT_STRUCTURE.md](../PROJECT_STRUCTURE.md) - პროექტის სტრუქტურა
 - [examples/ExtendedTables.php](../../app/Table/examples/ExtendedTables.php) - მაგალითები

---

## 💡 Pro Tips

1. **გამოიყენეთ localStorage** - პარამეტრები შეინახება ბრაუზერში
2. **დაამატეთ ვალიდაცია** - გამოიყენეთ TableHelper::validateConfig()
3. **Customize CSS** - შეცვალეთ `assets/css/table.css`
4. **Extend Class** - შექმენით custom Table subclass
5. **Cache Config** - დიდი პროექტებისთვის გამოიყენეთ caching

---

## 🤝 მხარდაჭერა

Issues? Questions? თავისუფლად შექმენით issue ან კონტაქტი.

**Happy Coding! 🎉**
