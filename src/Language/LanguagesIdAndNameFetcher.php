<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Language;

use Doctrine\DBAL\Connection;

final class LanguagesIdAndNameFetcher implements LanguagesIdAndNameFetcherInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * @return list<array{id: string, name: string}>
     */
    public function fetchLanguagesIdAndName(): array
    {
        $sql = 'SELECT LOWER(HEX(id)) AS id, name FROM language';
        /** @var array{id: string, name: string}[] $result */
        $result = $this->connection->executeQuery($sql)
            ->fetchAllAssociative();

        return $result;
    }
}
