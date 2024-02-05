<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater;

interface YesNoTranslationCaseWhenThenBuilderInterface
{
    /**
     * @param string|int|float|bool|null $default
     */
    public function buildCaseWhenThen(
        string $entityTable,
        string $entityTranslationTable,
        string $field,
        mixed $default,
        array &$parameters,
    ): string;
}
