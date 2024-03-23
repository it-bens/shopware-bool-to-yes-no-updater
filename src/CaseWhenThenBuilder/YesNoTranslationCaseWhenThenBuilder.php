<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder;

use ITB\SimpleWordsTranslator\Exception\NoTranslationFoundForNameException;
use ITB\SimpleWordsTranslator\TranslatorByNameInterface;

final class YesNoTranslationCaseWhenThenBuilder implements YesNoTranslationCaseWhenThenBuilderInterface
{
    public function __construct(
        private readonly TranslatorByNameInterface $translator
    ) {
    }

    public function buildCaseWhenThen(
        string $entityTable,
        string $entityTranslationTable,
        string $entityField,
        mixed $defaultValue,
        array $languages,
        string $defaultLanguageName,
        array &$parameters
    ): string {
        $caseWhenThen = 'CASE ';
        foreach ($languages as $i => $language) {
            $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId' . $i . ' AND `' . $entityTable . '`.`' . $entityField . '` = 1 THEN :translationYes' . $i . ' ';
            $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId' . $i . ' AND `' . $entityTable . '`.`' . $entityField . '` = 0 THEN :translationNo' . $i . ' ';

            $parameters['languageId' . $i] = $language['id'];
            try {
                $parameters['translationYes' . $i] = $this->translator->yes($language['name']);
            } catch (NoTranslationFoundForNameException) {
                $parameters['translationYes' . $i] = $this->translator->yes($defaultLanguageName);
            }
            try {
                $parameters['translationNo' . $i] = $this->translator->no($language['name']);
            } catch (NoTranslationFoundForNameException) {
                $parameters['translationNo' . $i] = $this->translator->no($defaultLanguageName);
            }
        }
        $caseWhenThen .= 'ELSE :default_for_' . $entityField . ' ';
        $parameters['default_for_' . $entityField] = $defaultValue;
        $caseWhenThen .= 'END';

        return $caseWhenThen;
    }
}
