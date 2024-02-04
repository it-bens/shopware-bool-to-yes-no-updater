<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Tests\Unit;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Generator;
use ITB\ShopwareBoolToYesNoUpdater\BoolToYesNoUpdater;
use ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder\YesNoTranslationCaseWhenThenBuilderInterface;
use ITB\ShopwareBoolToYesNoUpdater\Language\LanguagesIdAndNameFetcherInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BoolToYesNoUpdaterTest extends TestCase
{
    public static function updateProvider(): Generator
    {
        $entityTable = 'entity_table';
        $entityTranslationTable = 'entity_translation_table';
        $fields = ['field1', 'field2'];

        $languages = [
            [
                'id' => 'a7d3c3773f374092881a6f637cc3e33c',
                'name' => 'Deutsch',
            ],
            [
                'id' => 'c9d808f7fe60491d827cf1972e5626ea',
                'name' => 'English',
            ],
        ];

        $entityIds = ['3ca07a5bf2da4852bebe56cef47a7293', 'a9965144ffc54cf9b0dc5e4b0a0707d3'];

        $languagesIdAndNameFetcher = self::createStub(LanguagesIdAndNameFetcherInterface::class);
        $languagesIdAndNameFetcher->method('fetchLanguagesIdAndName')
            ->willReturn($languages);

        $caseWhenThens = array_map(static function (string $field) use ($entityTable, $entityTranslationTable): string {
            $caseWhenThen = 'CASE ';
            $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId0 AND `' . $entityTable . '`.`' . $field . '` = 1 THEN :translationYes0 ';
            $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId0 AND `' . $entityTable . '`.`' . $field . '` = 0 THEN :translationNo0 ';
            $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId1 AND `' . $entityTable . '`.`' . $field . '` = 1 THEN :translationYes1 ';
            $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId1 AND `' . $entityTable . '`.`' . $field . '` = 0 THEN :translationNo1 ';
            $caseWhenThen .= 'ELSE :default_for_' . $field . ' ';
            $caseWhenThen .= 'END';

            return $caseWhenThen;
        }, $fields);

        $setParts = array_map(static function (string $field, string $caseWhenThen) use ($entityTranslationTable): string {
            return $entityTranslationTable . '.' . $field . ' = ' . $caseWhenThen;
        }, $fields, array_values($caseWhenThens));

        $yesNoTranslationCaseWhenThenBuilder = self::createStub(YesNoTranslationCaseWhenThenBuilderInterface::class);
        $yesNoTranslationCaseWhenThenBuilder->method('buildCaseWhenThen')
            ->willReturnCallback(function (
                string $entityTable,
                string $entityTranslationTable,
                string $entityField,
                mixed $defaultValue,
                array $languages,
                array &$parameters
            ) use ($fields, $caseWhenThens) {
                $parameters['languageId1'] = 'a7d3c3773f374092881a6f637cc3e33c';
                $parameters['translationYes1'] = 'Ja';
                $parameters['translationNo1'] = 'Nein';
                $parameters['languageId2'] = 'c9d808f7fe60491d827cf1972e5626ea';
                $parameters['translationYes2'] = 'Yes';
                $parameters['translationNo2'] = 'No';

                $index = array_search($entityField, $fields, true);

                return $caseWhenThens[$index];
            });
        $yesNoTranslationCaseWhenThenBuilder->method('buildCaseWhenThen')
            ->willReturnOnConsecutiveCalls(...$caseWhenThens);

        $expectedSql = 'UPDATE `' . $entityTranslationTable . '` ';
        $expectedSql .= 'INNER JOIN `' . $entityTable . '` ON `' . $entityTable . '`.`id` = `' . $entityTranslationTable . '`.`' . $entityTable . '_id` ';
        $expectedSql .= 'SET ' . implode(', ', $setParts) . ' ';
        $expectedSql .= 'WHERE LOWER(HEX(`' . $entityTable . '`.`id`)) IN (:ids)';

        yield [
            $languagesIdAndNameFetcher,
            $yesNoTranslationCaseWhenThenBuilder,
            $entityTable,
            $entityTranslationTable,
            $fields,
            $entityIds,
            $languages,
            $expectedSql,
        ];
    }

    /**
     * @param string[] $fields
     * @param string[] $entityIds
     * @param array{id: string, name: string}[] $languages
     */
    #[DataProvider('updateProvider')]
    public function testUpdate(
        LanguagesIdAndNameFetcherInterface $languagesIdAndNameFetcher,
        YesNoTranslationCaseWhenThenBuilderInterface $yesNoTranslationCaseWhenThenBuilder,
        string $entityTable,
        string $entityTranslationTable,
        array $fields,
        array $entityIds,
        array $languages,
        string $expectedSql
    ): void {
        $connection = self::createStub(Connection::class);
        $connection->method('executeStatement')
            ->willReturnCallback(
                function (string $sql, array $params, array $types) use ($entityIds, $languages, $expectedSql): void {
                    self::assertSame($expectedSql, $sql);

                    self::assertSame($languages[0]['id'], $params['languageId1']);
                    self::assertSame('Ja', $params['translationYes1']);
                    self::assertSame('Nein', $params['translationNo1']);
                    self::assertSame($languages[1]['id'], $params['languageId2']);
                    self::assertSame('Yes', $params['translationYes2']);
                    self::assertSame('No', $params['translationNo2']);
                    self::assertSame($entityIds, $params['ids']);

                    self::assertSame(ArrayParameterType::BINARY, $types['ids']);
                }
            );

        $updater = new BoolToYesNoUpdater($connection, $languagesIdAndNameFetcher, $yesNoTranslationCaseWhenThenBuilder);

        $updater->update($entityTable, $entityTranslationTable, $fields, $entityIds);
    }
}
