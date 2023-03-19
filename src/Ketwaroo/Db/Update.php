<?php

namespace Ketwaroo\Db;

use Laminas\Db\Sql\Update as LaminasUpdate;
use Ketwaroo\Db as Kdb;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Predicate\PredicateSet;

class Update extends LaminasUpdate implements Sql {

    use TraitBuildSqlString;

    public function __construct(
        protected Kdb $db,
        $table = null,
        array $set = []
    ) {
        parent::__construct($table);
        if (!empty($table)) {
            $this->setSqlDirty();
        }

        if (!empty($set)) {
            $this->set($set);
        }
    }

    public function set(array $values, $flag = self::VALUES_SET): static {
        $this->setSqlDirty();
        return parent::set($values, $flag);
    }

    public function where($predicate, $combination = PredicateSet::OP_AND): static {
        $this->setSqlDirty();
        return parent::where($predicate, $combination);
    }

    public function query(array $parameters = []): ResultInterface {
        $result = $this->db->query($this, $parameters);
        $result->getResource()->closeCursor();
        return $result;
    }

}
