<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater\Language;

interface EntityTranslationsLanguagesIdAndNameFetcherInterface
{
    /**
     * @param list<string> $entityIds
     * @return list<array{id: string, name: string}>
     */
    public function fetchLanguagesIdAndName(string $entityTable, string $entityTranslationTable, array $entityIds): array;
}
