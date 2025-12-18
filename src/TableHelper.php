<?php

/**
 * ცხრილის დამხმარე ფუნქციები - კონფიგურაცია, sanitize, SQL ბილდერი
 */
class TableHelper {
    
    /**
     * ცხრილის კონფიგურაციის ვალიდაცია
     */
    public static function validateConfig($config) {
        $errors = [];
        
        if (empty($config['table'])) {
            $errors[] = "ცხრილის სახელი აუცილებელია";
        }
        
        if (empty($config['fields']) || !is_array($config['fields'])) {
            $errors[] = "ველების მასივი აუცილებელია";
        }
        
        if (isset($config['sortable']) && !is_array($config['sortable'])) {
            $errors[] = "Sortable უნდა იყოს მასივი";
        }
        
        return $errors;
    }
    
    /**
     * ცხრილის სახელის sanitize (SQL injection დაცვა)
     */
    public static function sanitizeTableName($tableName) {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $tableName);
    }
    
    /**
     * სვეტის სახელის sanitize (SQL injection დაცვა)
     */
    public static function sanitizeColumnName($columnName) {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $columnName);
    }
    
    /**
     * უნიკალური ცხრილის ID-ის გენერირება
     */
    public static function generateTableId($prefix = 'table') {
        return $prefix . '_' . uniqid();
    }
    
    /**
     * ველის მნიშვნელობის ფორმატირება ჩვენებისთვის
     */
    public static function formatValue($value, $type = 'text') {
        switch ($type) {
            case 'date':
                return date('Y-m-d', strtotime($value));
            case 'datetime':
                return date('Y-m-d H:i', strtotime($value));
            case 'boolean':
                return $value ? 'კი' : 'არა';
            case 'number':
                return number_format($value);
            case 'currency':
                return number_format($value, 2) . ' ₾';
            default:
                return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * WHERE ხელის აგება საძიებო ტერმინიდან
     */
    public static function buildSearchWhere($columns, $searchTerm, &$params) {
        if (empty($searchTerm) || empty($columns)) {
            return '';
        }
        
        $searchTerm = "%" . $searchTerm . "%";
        $likeParts = [];
        
        foreach ($columns as $col) {
            $sanitized = self::sanitizeColumnName($col);
            $likeParts[] = "$sanitized LIKE ?";
            $params[] = $searchTerm;
        }
        
        return !empty($likeParts) ? 'WHERE ' . implode(' OR ', $likeParts) : '';
    }
    
    /**
     * ORDER BY ხელის აგება
     */
    public static function buildOrderBy($sortField, $sortDir, $sortableColumns) {
        if (empty($sortField) || !in_array($sortField, $sortableColumns)) {
            return '';
        }
        
        $sanitizedField = self::sanitizeColumnName($sortField);
        $dir = strtoupper($sortDir) === 'DESC' ? 'DESC' : 'ASC';
        
        return "ORDER BY $sanitizedField $dir";
    }
    
    /**
     * Pagination მნიშვნელობების გამოთვლა
     */
    public static function calculatePagination($totalRecords, $page, $perPage) {
        $page = max(1, (int)$page);
        $perPage = max(1, min(100, (int)$perPage));
        $totalPages = ceil($totalRecords / $perPage);
        $offset = ($page - 1) * $perPage;
        
        return [
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords,
            'offset' => $offset,
            'hasNext' => $page < $totalPages,
            'hasPrev' => $page > 1
        ];
    }
    
    /**
     * კონფიგურაციის JSON ფაილში ექსპორტი
     */
    public static function exportConfig($config, $filename) {
        $json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($filename, $json);
    }
    
    /**
     * კონფიგურაციის JSON ფაილიდან იმპორტი
     */
    public static function importConfig($filename) {
        if (!file_exists($filename)) {
            return null;
        }
        
        $json = file_get_contents($filename);
        return json_decode($json, true);
    }
}
