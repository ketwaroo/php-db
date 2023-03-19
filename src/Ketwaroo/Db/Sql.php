<?php

namespace Ketwaroo\Db;
use Laminas\Db\Adapter\Driver\ResultInterface;

interface Sql extends \Stringable {

    public function query(array $parameters = []): ResultInterface;
}
