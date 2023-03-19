<?php

namespace Ketwaroo\Db;

use Laminas\Db\Adapter\ParameterContainer;

trait TraitBuildSqlString {

    protected bool $isSqlDirty   = false;
    protected ?ParameterContainer $parameters;
    protected string $sqlString = '';

    public function getParameters(): ParameterContainer {
        if (!isset($this->parameters)) {
            $this->parameters = new ParameterContainer([]);
        }
        return $this->parameters;
    }

    protected function isSqlDirty(): bool {
        return $this->isSqlDirty;
    }

    protected function setSqlDirty(): static {
        $this->isSqlDirty = true;
        return $this;
    }

    protected function clearSqlDirty(): static {
        $this->isSqlDirty = false;
        return $this;
    }

    public function __toString() {
        /** @var \Laminas\Db\Sql\AbstractSql|TraitBuildSqlString $this */
        if ($this->isSqlDirty()) {
            $this->sqlString = $this->buildSqlString($this->db->getPlatform(), $this->db->getDriver());
            $this->clearSqlDirty();
        }
        return $this->sqlString;
    }

}
