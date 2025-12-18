<?php

/**
 * ცხრილის მოდელი - მონაცემების მიღება, sorting, pagination, search
 */
class TableModel {
    private $db;
    private $tableName;
    private $columns = [];
    private $sortable = [];
    private $pageSize = 10;

    /**
     * კონსტრუქტორი - PDO კავშირი აუცილებელი
     * 
     * @param PDO $pdo PDO კავშირი ბაზესთან
     * @param string $tableName ბაზის ცხრილის სახელი
     * @param array $columns სვეტების სია
     * @param array $sortable sortable სვეტების სია
     */
    public function __construct($pdo, $tableName, $columns = [], $sortable = []) {
        $this->db = $pdo;
        $this->tableName = $tableName;
        $this->columns = $columns;
        $this->sortable = $sortable;
    }

    public function setPageSize($size) {
        $size = (int)$size;
        if ($size > 0) {
            $this->pageSize = $size;
        }
    }

    /**
     * მონაცემის მიღება pagination-ით
     */
    public function getData($page = 1, $search = '', $sortField = '', $sortDir = 'ASC') {
        $offset = max(0, ($page - 1) * $this->pageSize);

        $whereSql = '';
        $params = [];
        if (!empty($search)) {
            $search = "%" . $search . "%";
            $likeParts = [];
            foreach ($this->columns as $col) {
                $likeParts[] = "$col LIKE ?";
                $params[] = $search;
            }
            if (!empty($likeParts)) {
                $whereSql = 'WHERE ' . implode(' OR ', $likeParts);
            }
        }

        $orderSql = '';
        if (!empty($sortField) && in_array($sortField, $this->sortable)) {
            $dir = strtoupper($sortDir) === 'DESC' ? 'DESC' : 'ASC';
            $orderSql = "ORDER BY $sortField $dir";
        }

        $sql = "SELECT " . implode(', ', $this->columns) . " FROM {$this->tableName} $whereSql $orderSql LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);

        $bindIndex = 1;
        foreach ($params as $p) {
            $stmt->bindValue($bindIndex++, $p);
        }
        $stmt->bindValue($bindIndex++, (int)$this->pageSize, PDO::PARAM_INT);
        $stmt->bindValue($bindIndex++, (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalRecords = $this->getTotalRecords($whereSql, $params);
        $totalPages = (int)ceil($totalRecords / $this->pageSize);

        return [
            'data' => $data,
            'total_records' => $totalRecords,
            'total_pages' => $totalPages,
            'current_page' => (int)$page,
        ];
    }

    private function getTotalRecords($whereSql = '', $params = []) {
        $sql = "SELECT COUNT(*) as cnt FROM {$this->tableName} $whereSql";
        $stmt = $this->db->prepare($sql);
        $i = 1;
        foreach ($params as $p) {
            $stmt->bindValue($i++, $p);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$row['cnt'];
    }
}
