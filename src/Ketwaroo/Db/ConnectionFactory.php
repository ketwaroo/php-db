<?php

namespace Ketwaroo\Db;

use Ketwaroo\Db;
use Ketwaroo\Db\Driver\Sqlite\Connection as SqliteConnection;
use Ketwaroo\Db\Statement;
use Ketwaroo\Db\Result;

class ConnectionFactory {

    public static function sqlite(string $dbFile, array $initialQueries = [], bool $useWal = true): Db {
        $dsn = 'sqlite:' . $dbFile;
        if ($useWal) {
            $initialQueries[] = 'PRAGMA journal_mode=WAL';
        }
        return new Db(
            new SqliteConnection([
                'dsn' => $dsn,
                ]),
            $initialQueries
        );
    }

}
