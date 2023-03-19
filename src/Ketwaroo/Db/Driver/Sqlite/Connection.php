<?php

namespace Ketwaroo\Db\Driver\Sqlite;

use Laminas\Db\Adapter\Driver\Pdo\Connection as LaminasPdoConnection;
use Laminas\Db\Adapter\Exception\RuntimeException;

class Connection extends LaminasPdoConnection {

    /**
     * {@inheritdoc}
     */
    public function beginTransaction() {

        if (!$this->isConnected()) {
            $this->connect();
        }

        if (0 === $this->nestedTransactionsCount) {
            $this->resource->exec('BEGIN IMMEDIATE TRANSACTION');
            $this->inTransaction = true;
        }

        $this->nestedTransactionsCount++;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function commit() {
        if (!$this->isConnected()) {
            $this->connect();
        }

        if ($this->inTransaction) {
            $this->nestedTransactionsCount -= 1;
        }

        /*
         * This shouldn't check for being in a transaction since
         * after issuing a SET autocommit=0; we have to commit too.
         */
        if (0 === $this->nestedTransactionsCount) {
            $this->resource->exec('COMMIT');
            $this->inTransaction = false;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function rollBack() {

        if (!$this->isConnected()) {
            throw new RuntimeException('Must be connected before you can rollback');
        }

        if (!$this->inTransaction()) {
            throw new RuntimeException('Must call beginTransaction() before you can rollback');
        }

        $this->resource->exec('ROLLBACK');

        $this->inTransaction           = false;
        $this->nestedTransactionsCount = 0;

        return $this;
    }

}
