<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Tests\Mock;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\SQLite3\Driver;
use Doctrine\DBAL\Result;
use Exception;

final class DbalConnection extends Connection
{
    public function __construct()
    {
        parent::__construct([], new Driver());
    }

    public function executeQuery(string $sql, array $params = [], $types = [], ?QueryCacheProfile $qcp = null): Result
    {
        throw new Exception('Not implemented');
    }

    public function executeStatement($sql, array $params = [], array $types = [])
    {
        throw new Exception('Not implemented');
    }
}
