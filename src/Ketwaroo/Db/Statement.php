<?php

namespace Ketwaroo\Db;

use Laminas\Db\Adapter\Driver\Pdo\Statement as LamiansPdoStatement;
use Laminas\Db\Adapter\ParameterContainer;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Db\Adapter\Driver\Pdo\Result;

/**
 * @param null|array|ParameterContainer $parameters
 * @throws Exception\InvalidQueryException
 * @return Result
 */
class Statement extends LamiansPdoStatement {

    protected function bindParametersFromContainer() {
        // pointless if you can't reuse prepared statement with different params...
        $this->parametersBound = false;
        parent::bindParametersFromContainer();
    }

    public function execute($parameters = null) {

        if ($parameters instanceof ParameterContainer) {
            $this->parameterContainer = $parameters;
            $parameters               = null;
        }
        else {
            $this->parameterContainer = new ParameterContainer();
        }

        if (is_array($parameters)) {
            $this->parameterContainer->setFromArray($parameters);
        }

        if ($this->parameterContainer->count() > 0) {
            $this->bindParametersFromContainer();
        }

        return parent::execute($parameters);
    }

}
