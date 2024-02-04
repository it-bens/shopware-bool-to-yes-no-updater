<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder;

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
        array &$parameters
    ): string {
        $caseWhenThen = 'CASE ';
        foreach ($languages as $i => $language) {
            $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId' . $i . ' AND `' . $entityTable . '`.`' . $entityField . '` = 1 THEN :translationYes' . $i . ' ';
            $caseWhenThen .= 'WHEN HEX(`' . $entityTranslationTable . '`.`language_id`) = :languageId' . $i . ' AND `' . $entityTable . '`.`' . $entityField . '` = 0 THEN :translationNo' . $i . ' ';

            $parameters['languageId' . $i] = $language['id'];
            $parameters['translationYes' . $i] = $this->translator->yes($language['name']);
            $parameters['translationNo' . $i] = $this->translator->no($language['name']);
        }
        $caseWhenThen .= 'ELSE :default_for_' . $entityField . ' ';
        $parameters['default_for_' . $entityField] = $defaultValue;
        $caseWhenThen .= 'END';

        return $caseWhenThen;
    }
}
