<?php

namespace Ketwaroo\Db;

use Laminas\Db\Adapter\Driver\Pdo\Result as LaminasResult;

class Result extends LaminasResult {

    public function fetchColumn(int $column = 0): mixed {
        $prevFetch = $this->getFetchMode();
        $this->setFetchMode(\PDO::FETCH_NUM);
        $row       = $this->current();
        $val       = false;
        if (is_array($row) && array_key_exists($column, $row)) {
            $val = $row[$column];
        }
        $this->setFetchMode($prevFetch);
        return $val;
    }

    public function fetchAll($mode = \PDO::FETCH_ASSOC): array {
        $all       = [];
        $prevFetch = $this->getFetchMode();
        $this->setFetchMode($mode);
        foreach ($this as $row) {

            $all[] = $row;
        }
        $this->setFetchMode($prevFetch);
        $this->getResource()->closeCursor();
        return $all;
    }

    /**
     * 
     * @return \PDOStatement
     */
    public function getResource() {
        return parent::getResource();
    }

}
