<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Tests\Unit\Language;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use ITB\ShopwareBoolToYesNoUpdater\Language\EntityTranslationsLanguagesIdAndNameFetcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EntityTranslationsLanguagesIdAndNameFetcherTest extends TestCase
{
    public static function fetchLanguagesIdAndNameProvider(): \Generator
    {
        yield 'empty' => [
            'product_extension_mcw_product_properties',
            'product_extension_mcw_product_properties_translation',
            ['4b0d6f7016654c046a64692bc5ca5ec'],
            [],
        ];
        yield 'one language' => [
            'product_extension_mcw_product_properties',
            'product_extension_mcw_product_properties_translation',
            ['4b0d6f7016654c046a64692bc5ca5ec'],
            [
                [
                    'id' => 'a7d3c3773f374092881a6f637cc3e33c',
                    'name' => 'Deutsch',
                ],
            ],
        ];
        yield 'two languages' => [
            'product_extension_mcw_product_properties',
            'product_extension_mcw_product_properties_translation',
            ['4b0d6f7016654c046a64692bc5ca5ec'],
            [
                [
                    'id' => 'a7d3c3773f374092881a6f637cc3e33c',
                    'name' => 'Deutsch',
                ],
                [
                    'id' => 'c9d808f7fe60491d827cf1972e5626ea',
                    'name' => 'English',
                ],
            ],
        ];
    }

    /**
     * @param array{id: string, name: string}[] $expectedLanguages
     */
    #[DataProvider('fetchLanguagesIdAndNameProvider')]
    public function testFetchLanguagesIdAndName(
        string $entityTable,
        string $entityTranslationTable,
        array $entityIds,
        array $expectedLanguages
    ): void {
        $connection = $this->createStub(Connection::class);
        $connection->method('executeQuery')
            ->willReturnCallback(function (string $sql) use ($entityTable, $entityTranslationTable, $expectedLanguages) {
                $expectedSql = 'SELECT DISTINCT LOWER(HEX(`language`.`id`)) AS id, `language`.`name` FROM `' . $entityTranslationTable . '` LEFT JOIN `language` ON `language`.`id` = `' . $entityTranslationTable . '`.`language_id` WHERE LOWER(HEX(`' . $entityTranslationTable . '`.`' . $entityTable . '_id`)) IN (:ids)';

                self::assertSame($expectedSql, $sql);

                $result = $this->createStub(Result::class);
                $result->method('fetchAllAssociative')
                    ->willReturn($expectedLanguages);

                return $result;
            });

        $languagesIdAndNameFetcher = new EntityTranslationsLanguagesIdAndNameFetcher($connection);
        $languages = $languagesIdAndNameFetcher->fetchLanguagesIdAndName($entityTable, $entityTranslationTable, $entityIds);
        $this->assertSame($expectedLanguages, $languages);
    }
}
