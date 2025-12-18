<?php

/**
 * ცხრილის მოდული - განაახლებადი ცხრილი AJAX, sorting, pagination, search
 */
class Table {
    private $tableId;
    private $config;
    private $basePath;
    
    public function __construct($tableId, $config = []) {
        $this->tableId = $tableId;
        $this->config = $config;
        $this->basePath = dirname(__FILE__);
        
        // ნაგულისხმევი კონფიგურაცია
        $this->config = array_merge([
            'table' => '',
            'fields' => [],
            'sortable' => [],
            'perPageOptions' => [5, 10, 20, 50],
            'defaultPerPage' => 10,
            'searchPlaceholder' => 'ძიება',
            'columnsButtonText' => 'სვეტები ▾',
            'apiUrl' => '/api/table'
        ], $this->config);
    }
    
    /**
     * ცხრილის HTML-ის რენდერი კონტროლებით
     */
    public function render($tableName = null) {
        if ($tableName) {
            $this->setTableName($tableName);
        }
        ob_start();
        include $this->basePath . '/views/table.php';
        return ob_get_clean();
    }
    
    /**
     * ცხრილის კონფიგურაციის JSON-ად დაბრუნება
     */
    public function getConfigJson() {
        return json_encode([
            'table' => $this->config['table'],
            'columns' => array_keys($this->config['fields']),
            'headers' => $this->config['fields'],
            'sortable' => $this->config['sortable']
        ], JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * საჭირო CSS ფაილების ჩართვა
     */
    public function includeCss() {
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        return '<link rel="stylesheet" href="' . $base . '/Table/assets/css/table.css">';
    }
    
    /**
     * საჭირო JS ფაილების ჩართვა და ინიციალიზაცია
     */
    public function includeJs() {
        $config = $this->getConfigJson();
        $apiUrl = $this->config['apiUrl'];
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        
        return <<<HTML
<script>
window.TABLE_CONFIGS = window.TABLE_CONFIGS || {};
window.TABLE_CONFIGS['{$this->tableId}'] = {$config};
window.CONTROLLER_URL = '{$apiUrl}';
</script>
<script src="{$base}/Table/assets/js/table.js"></script>
HTML;
    }
    
    /**
     * ცხრილის ID-ის დაბრუნება
     */
    public function getTableId() {
        return $this->tableId;
    }

    /**
     * ცხრილის სახელის/ID-ის დაყენება
     */
    public function setTableName($name) {
        if (!empty($name)) {
            $this->tableId = $name;
            $this->config['table'] = $name;
        }
        return $this;
    }
    
    /**
     * კონფიგურაციის მნიშვნელობის დაბრუნება
     */
    public function getConfig($key, $default = null) {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }
    
    /**
     * კონფიგურაციის მნიშვნელობის დაყენება
     */
    public function setConfig($key, $value) {
        $this->config[$key] = $value;
        return $this;
    }
    
    /**
     * sortable სვეტის დამატება
     */
    public function addSortable($column) {
        if (!in_array($column, $this->config['sortable'])) {
            $this->config['sortable'][] = $column;
        }
        return $this;
    }
    
    /**
     * sortable სვეტის წაშლა
     */
    public function removeSortable($column) {
        $this->config['sortable'] = array_values(
            array_filter($this->config['sortable'], function($col) use ($column) {
                return $col !== $column;
            })
        );
        return $this;
    }
    
    /**
     * ველი/სვეტის დამატება
     */
    public function addField($key, $label) {
        $this->config['fields'][$key] = $label;
        return $this;
    }
    
    /**
     * ველი/სვეტის წაშლა
     */
    public function removeField($key) {
        unset($this->config['fields'][$key]);
        return $this;
    }
    
    /**
     * API URL-ის დაყენება
     */
    public function setApiUrl($url) {
        $this->config['apiUrl'] = $url;
        return $this;
    }
    
    /**
     * ძიების ჩართვა/გამორთვა
     */
    public function setSearchEnabled($enabled = true) {
        $this->config['searchEnabled'] = $enabled;
        return $this;
    }
    
    /**
     * pagination-ის ჩართვა/გამორთვა
     */
    public function setPaginationEnabled($enabled = true) {
        $this->config['paginationEnabled'] = $enabled;
        return $this;
    }
}
