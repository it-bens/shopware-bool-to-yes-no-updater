<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\CaseWhenThenBuilder;

interface YesNoTranslationCaseWhenThenBuilderInterface
{
    /**
     * @param string|int|float|bool|null $defaultValue
     * @param list<array{id: string, name: string}> $languages
     */
    public function buildCaseWhenThen(
        string $entityTable,
        string $entityTranslationTable,
        string $entityField,
        mixed $defaultValue,
        array $languages,
        array &$parameters,
    ): string;
}
