<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Language\LanguageDefinition;

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
        $queryBuilder = $this->connection->createQueryBuilder()
            ->select('id')
            ->addSelect('name')
            ->from(LanguageDefinition::ENTITY_NAME);

        /** @var array{id: string, name: string}[] $result */
        $result = $queryBuilder->executeQuery()
            ->fetchAllAssociative();

        return array_values(array_map(static function (array $language): array {
            return [
                'id' => Uuid::fromBytesToHex($language['id']),
                'name' => $language['name'],
            ];
        }, $result));
    }
}
