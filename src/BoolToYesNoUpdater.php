<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\DataAbstractionLayer\Doctrine\RetryableQuery;

final class BoolToYesNoUpdater implements BoolToYesNoUpdaterInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly YesNoTranslationCaseWhenThenBuilderInterface $yesNoTranslationCaseWhenThenBuilder,
    ) {
    }

    /**
     * @param list<string> $ids
     */
    public function update(string $entityTable, string $entityTranslationTable, array $fields, array $ids): void
    {
        $updatePart = 'UPDATE ' . $entityTranslationTable . ' ';
        $joinPart = 'INNER JOIN ' . $entityTable . ' ON ' . $entityTable . '.id = ' . $entityTranslationTable . '.' . $entityTable . '_id ';

        $parameters = [];
        $setParts = [];
        foreach ($fields as $field) {
            $caseWhen = $this->yesNoTranslationCaseWhenThenBuilder->buildCaseWhenThen(
                $entityTable,
                $entityTranslationTable,
                $field,
                null,
                $parameters
            );
            $setParts[] = $entityTranslationTable . '.' . $field . ' = ' . $caseWhen;
        }

        $wherePart = 'WHERE LOWER(HEX(' . $entityTable . '.id)) IN (:ids)';
        $parameters['ids'] = $ids;

        $updateSQL = $updatePart . $joinPart . 'SET ' . implode(', ', $setParts) . ' ' . $wherePart;

        RetryableQuery::retryable($this->connection, function () use ($updateSQL, $parameters): void {
            $this->connection->executeStatement($updateSQL, $parameters, [
                'ids' => ArrayParameterType::BINARY,
            ]);
        });
    }
}
