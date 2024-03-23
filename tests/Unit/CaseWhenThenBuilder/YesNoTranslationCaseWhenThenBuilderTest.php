<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Tests\Unit\CaseWhenThenBuilder;

use ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder\YesNoTranslationCaseWhenThenBuilder;
use ITB\SimpleWordsTranslator\Exception\NoTranslationFoundForNameException;
use ITB\SimpleWordsTranslator\Translation\De;
use ITB\SimpleWordsTranslator\TranslatorByName;
use ITB\SimpleWordsTranslator\TranslatorByNameInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class YesNoTranslationCaseWhenThenBuilderTest extends TestCase
{
    public static function buildCaseWhenThenProvider(): \Generator
    {
        $entityTable = 'entity_table';
        $entityTranslationTable = 'entity_translation_table';
        $field = 'field1';

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

        $caseWhenThen = 'CASE ';
        $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId0 AND `' . $entityTable . '`.`' . $field . '` = 1 THEN :translationYes0 ';
        $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId0 AND `' . $entityTable . '`.`' . $field . '` = 0 THEN :translationNo0 ';
        $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId1 AND `' . $entityTable . '`.`' . $field . '` = 1 THEN :translationYes1 ';
        $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId1 AND `' . $entityTable . '`.`' . $field . '` = 0 THEN :translationNo1 ';
        $caseWhenThen .= 'ELSE :default_for_' . $field . ' ';
        $caseWhenThen .= 'END';

        $parameters = [
            'languageId0' => 'a7d3c3773f374092881a6f637cc3e33c',
            'translationYes0' => 'Ja',
            'translationNo0' => 'Nein',
            'languageId1' => 'c9d808f7fe60491d827cf1972e5626ea',
            'translationYes1' => 'Yes',
            'translationNo1' => 'No',
            'default_for_' . $field => null,
        ];

        $yesNoTranslationCaseWhenThenBuilder = new YesNoTranslationCaseWhenThenBuilder(new TranslatorByName());

        yield 'all languages are known' => [
            $yesNoTranslationCaseWhenThenBuilder,
            $entityTable,
            $entityTranslationTable,
            $field,
            null,
            $languages,
            De::name(),
            $caseWhenThen,
            $parameters,
        ];

        $languages = [
            [
                'id' => '649fb5b058124a5b86ec0a88a4072209',
                'name' => 'Svenska',
            ],
        ];

        $caseWhenThen = 'CASE ';
        $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId0 AND `' . $entityTable . '`.`' . $field . '` = 1 THEN :translationYes0 ';
        $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId0 AND `' . $entityTable . '`.`' . $field . '` = 0 THEN :translationNo0 ';
        $caseWhenThen .= 'ELSE :default_for_' . $field . ' ';
        $caseWhenThen .= 'END';

        $parameters = [
            'languageId0' => '649fb5b058124a5b86ec0a88a4072209',
            'translationYes0' => 'Ja',
            'translationNo0' => 'Nein',
            'default_for_' . $field => null,
        ];

        $translator = self::createStub(TranslatorByNameInterface::class);
        $translator->method('yes')
            ->willReturnCallback(static fn (string $name) => match ($name) {
                'deutsch' => 'Ja',
                'english' => 'Yes',
                default => throw new NoTranslationFoundForNameException($name),
            });
        $translator->method('no')
            ->willReturnCallback(static fn (string $name) => match ($name) {
                'deutsch' => 'Nein',
                'english' => 'No',
                default => throw new NoTranslationFoundForNameException($name),
            });
        $yesNoTranslationCaseWhenThenBuilder = new YesNoTranslationCaseWhenThenBuilder($translator);

        yield 'the language is unknown' => [
            $yesNoTranslationCaseWhenThenBuilder,
            $entityTable,
            $entityTranslationTable,
            $field,
            null,
            $languages,
            De::name(),
            $caseWhenThen,
            $parameters,
        ];
    }

    /**
     * @param array{id: string, name: string}[] $languages
     * @param array<string, mixed> $expectedParameters
     */
    #[DataProvider('buildCaseWhenThenProvider')]
    public function testBuildCaseWhenThen(
        YesNoTranslationCaseWhenThenBuilder $yesNoTranslationCaseWhenThenBuilder,
        string $entityTable,
        string $entityTranslationTable,
        string $entityField,
        bool|float|int|string|null $defaultValue,
        array $languages,
        string $defaultLanguage,
        string $expectedWhenCaseThen,
        array $expectedParameters,
    ): void {
        $parameters = [];
        $whenCaseThen = $yesNoTranslationCaseWhenThenBuilder->buildCaseWhenThen(
            $entityTable,
            $entityTranslationTable,
            $entityField,
            $defaultValue,
            $languages,
            $defaultLanguage,
            $parameters
        );

        self::assertSame($expectedWhenCaseThen, $whenCaseThen);
        self::assertSame($expectedParameters, $parameters);
    }
}
