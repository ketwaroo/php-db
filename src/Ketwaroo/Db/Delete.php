<?php

namespace Ketwaroo\Db;

use Laminas\Db\Sql\Delete as LaminasSelect;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Predicate\PredicateSet;
use Ketwaroo\Db as Kdb;

class Delete extends LaminasSelect implements Sql {

    use TraitBuildSqlString;

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

    /**
     * 
     * @param array $parameters
     * @return ResultInterface
     */
    public function query(array $parameters = []): ResultInterface {

        return $this->db->query($this, $parameters);
    }

}
