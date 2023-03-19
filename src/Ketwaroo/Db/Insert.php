<?php

namespace Ketwaroo\Db;

use Ketwaroo\Db as Kdb;
use Laminas\Db\Sql\Insert as LaminasInsert;
use Laminas\Db\Adapter\Driver\ResultInterface;


class Insert extends LaminasInsert implements Sql {

    use \Ketwaroo\Db\TraitBuildSqlString;

    public function __construct(
        protected Kdb $db,
        $table = null,
        array $values = [],
    ) {
        parent::__construct($table);
        if (!empty($table)) {
            $this->setSqlDirty();
        }
        $this->values($values);
    }

    public function into($table): static {
        $this->setSqlDirty();
        return parent::into($table);
    }
    public function columns(array $columns): static {
        $this->setSqlDirty();
        return parent::columns($columns);
    }
    public function values($values, $flag = self::VALUES_SET): static {
        $this->setSqlDirty();
        return parent::values($values, $flag);
    }

    public function query(array $parameters = []): ResultInterface {
        $result = $this->db->query($this, $parameters);
        $result->getResource()->closeCursor();
        return $result;
    }

}
