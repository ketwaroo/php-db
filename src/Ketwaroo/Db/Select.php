<?php

namespace Ketwaroo\Db;

use Ketwaroo\Db as Kdb;
use Laminas\Db\Sql\Select as LaminasSelect;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Driver\StatementInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Ketwaroo\Db\Result;

class Select extends LaminasSelect implements Sql {

    use \Ketwaroo\Db\TraitBuildSqlString;

    public function __construct(
        protected Kdb $db,
        $table = null
    ) {
        parent::__construct($table);
        if (!empty($table)) {
            $this->setSqlDirty();
        }
    }

    public function from($table): static {
        $this->setSqlDirty();
        return parent::from($table);
    }

    public function where($predicate, $combination = PredicateSet::OP_AND): static {
        $this->setSqlDirty();
        return parent::where($predicate, $combination);
    }

    public function join($name, $on, $columns = self::SQL_STAR, $type = self::JOIN_INNER): static {
        $this->setSqlDirty();
        return parent::join($name, $on, $columns, $type);
    }

    public function group($group): static {
        $this->setSqlDirty();
        return parent::group($group);
    }

    public function query(array $parameters = []): Result {

        return $this->db->query($this, $parameters);
    }

    public function fetchAll(array $parameters = [], $mode = \PDO::FETCH_ASSOC) {
        return $this->query($parameters)->fetchAll($mode);
    }

}
