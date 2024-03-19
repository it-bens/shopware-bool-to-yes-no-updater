<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Language;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

final class EntityTranslationsLanguagesIdAndNameFetcher implements EntityTranslationsLanguagesIdAndNameFetcherInterface
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function fetchLanguagesIdAndName(string $entityTable, string $entityTranslationTable, array $entityIds): array
    {
        $selectPart = 'SELECT DISTINCT LOWER(HEX(`language`.`id`)) AS id, `language`.`name` FROM `' . $entityTranslationTable . '` ';
        $joinPart = 'LEFT JOIN `language` ON `language`.`id` = `' . $entityTranslationTable . '`.`language_id` ';
        $wherePart = 'WHERE LOWER(HEX(`' . $entityTranslationTable . '`.`' . $entityTable . '_id`)) IN (:ids)';

        $sql = $selectPart . $joinPart . $wherePart;
        $parameters['ids'] = $entityIds;

        /** @var array{id: string, name: string}[] $result */
        $result = $this->connection->executeQuery($sql, $parameters, [
            'ids' => ArrayParameterType::BINARY,
        ])
            ->fetchAllAssociative();

        return $result;
    }
}
