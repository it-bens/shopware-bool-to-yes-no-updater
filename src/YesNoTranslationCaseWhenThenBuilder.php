<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater;

use ITB\SimpleWordsTranslator\TranslatorByNameInterface;
use Shopware\Core\Framework\Uuid\Uuid;

final class YesNoTranslationCaseWhenThenBuilder implements YesNoTranslationCaseWhenThenBuilderInterface
{
    public function __construct(
        private readonly LanguagesIdAndNameFetcherInterface $languagesIdAndNameFetcher,
        private readonly TranslatorByNameInterface $translator
    ) {
    }

    public function buildCaseWhenThen(
        string $entityTable,
        string $entityTranslationTable,
        string $field,
        mixed $default,
        array &$parameters
    ): string {
        $languages = $this->languagesIdAndNameFetcher->fetchLanguagesIdAndName();

        $caseWhenThen = 'CASE ';
        foreach ($languages as $i => $language) {
            $caseWhenThen .= 'WHEN ' . $entityTranslationTable . '.language_id = :languageId' . $i . ' AND ' . $entityTable . '.' . $field . ' = 1 THEN :translationYes' . $i . ' ';
            $caseWhenThen .= 'WHEN ' . $entityTranslationTable . '.language_id = :languageId' . $i . ' AND ' . $entityTable . '.' . $field . ' = 0 THEN :translationNo' . $i . ' ';

            $parameters['languageId' . $i] = Uuid::fromHexToBytes($language['id']);
            $parameters['translationYes' . $i] = $this->translator->yes($language['name']);
            $parameters['translationNo' . $i] = $this->translator->no($language['name']);
        }
        $caseWhenThen .= 'ELSE :default_for_' . $field . ' ';
        $parameters['default_for_' . $field] = $default;
        $caseWhenThen .= 'END';

        return $caseWhenThen;
    }
}
