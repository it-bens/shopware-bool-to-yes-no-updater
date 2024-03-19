<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater;

interface BoolToYesNoUpdaterInterface
{
    /**
     * @param list<array{id: string, name: string}> $languages
     * @param list<string> $fields
     * @param list<string> $ids
     */
    public function update(array $languages, string $entityTable, string $entityTranslationTable, array $fields, array $ids): void;
}
