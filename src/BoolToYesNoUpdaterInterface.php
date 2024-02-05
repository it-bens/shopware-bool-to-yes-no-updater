<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater;

interface BoolToYesNoUpdaterInterface
{
    /**
     * @param list<string> $fields
     * @param list<string> $ids
     */
    public function update(string $entityTable, string $entityTranslationTable, array $fields, array $ids): void;
}
