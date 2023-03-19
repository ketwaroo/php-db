<?php

namespace Ketwaroo;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Platform\PlatformInterface;
use Laminas\Db\Adapter\Driver\Pdo\Pdo;
use Laminas\Db\Adapter\Driver\DriverInterface;
use Ketwaroo\Db\Sql;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\Sql\Expression;
use Ketwaroo\Db\Statement;
use Ketwaroo\Db\Result;
use Ketwaroo\Db\Driver\Sqlite\Connection;
use Laminas\Db\Adapter\Driver\Pdo\Connection as LaminasPdoConnection;
use Laminas\Db\Sql\Where;
class Db {

    protected Adapter $adapter;
    protected PlatformInterface $platform;
    protected Pdo $driver;

    /**
     * 
     * @var Statement|array
     */
    static array $preparedStatements = [];

    public function __construct(
        protected LaminasPdoConnection|string $connection,
        array $initialQueries = []
    ) {

        if (is_string($connection)) {
            $connection = new LaminasPdoConnection(['dsn' => $connection]);
        }

        $this->adapter = new Adapter(
            new Pdo(
                $connection,
                new Statement(),
                new Result()
            )
        );

        $this->platform = $this->adapter->getPlatform();
        $this->driver   = $this->adapter->getDriver();
        foreach ($initialQueries as $q) {
            $this->getDriver()->getConnection()->execute($q);
        }
    }

    public function getAdapter(): Adapter {
        return $this->adapter;
    }

    public function getPlatform(): PlatformInterface {
        return $this->platform;
    }

    public function getDriver(): DriverInterface {
        return $this->driver;
    }

    public function quote($value) {
        return $this->getPlatform()->quoteValue($value);
    }

    public function quoteIdentifier($value) {
        return $this->getPlatform()->quoteIdentifier($value);
    }

    public function expr(string $expression, array|string|null $parameters = null, array $types = []): Expression {
        return new Expression($expression, $parameters, $types);
    }
    
    public function predicate(): Where {
        return new Where();
    }

    public function select($table = null): Db\Select {
        return new Db\Select(
            $this,
            $table
        );
    }

    public function insert($table, array $values): Db\Insert {
        return new Db\Insert($this, $table, $values);
    }

    public function update($table, array $set): Db\Update {
        return new Db\Update($this, $table, $set);
    }

    public function delete($table, array $set): Db\Delete {
        return new Db\Delete($this, $table);
    }

    public function createPreparedStatement(Sql|string $sql): Statement {
        $prep = $this->getDriver()->createStatement();
        $prep->prepare((string)$sql);
        return $prep;
    }
    
    public function execute(Sql|string $sql, array $parameters = []): mixed {

        $prep           = $this->createPreparedStatement($sql);
        $result         = $prep->execute($parameters);
        $generatedValue = $result->getGeneratedValue();
        $result->getResource()->closeCursor();
        return $generatedValue;
    }

    public function query(Sql|string $sql, array $parameters = [], bool $keepPrepared = false): Result {

        if (!$keepPrepared) {
            $prep = $this->createPreparedStatement($sql);
        }
        else {
            $sql = strval($sql);
            $key = sha1($sql);
            if (!isset(static::$preparedStatements[$key])) {
                $prep                             = $this->createPreparedStatement($sql);
                static::$preparedStatements[$key] = $prep;
            }
            /** @var Statement $prep */
            $prep = static::$preparedStatements[$key];
        }

        return $prep->execute(new ParameterContainer($parameters));
    }

    /**
     * 
     * @return \Laminas\Db\Adapter\Driver\ConnectionInterface
     */
    public function beginTransaction() {
        return $this->getDriver()->getConnection()->beginTransaction();
    }

    public function commitTransaction() {
        return $this->getDriver()->getConnection()->commit();
    }

}
