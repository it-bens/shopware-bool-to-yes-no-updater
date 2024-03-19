<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Tests\Unit\Language;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use ITB\ShopwareBoolToYesNoUpdater\Language\AllLanguagesIdAndNameFetcher;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AllLanguagesIdAndNameFetcherTest extends TestCase
{
    public static function fetchLanguagesIdAndNameProvider(): array
    {
        return [
            'empty' => [[]],
            'one language' => [
                [
                    [
                        'id' => 'a7d3c3773f374092881a6f637cc3e33c',
                        'name' => 'Deutsch',
                    ],
                ],
            ],
            'two languages' => [
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
            ],
        ];
    }

    /**
     * @param array{id: string, name: string}[] $expectedLanguages
     */
    #[DataProvider('fetchLanguagesIdAndNameProvider')]
    public function testFetchLanguagesIdAndName(array $expectedLanguages): void
    {
        $connection = $this->createStub(Connection::class);
        $connection->method('executeQuery')
            ->willReturnCallback(function (string $sql) use ($expectedLanguages) {
                self::assertSame('SELECT LOWER(HEX(id)) AS id, name FROM language', $sql);

                $result = $this->createStub(Result::class);
                $result->method('fetchAllAssociative')
                    ->willReturn($expectedLanguages);

                return $result;
            });

        $languagesIdAndNameFetcher = new AllLanguagesIdAndNameFetcher($connection);
        $languages = $languagesIdAndNameFetcher->fetchLanguagesIdAndName();
        $this->assertSame($expectedLanguages, $languages);
    }
}
