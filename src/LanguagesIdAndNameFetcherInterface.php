<?php

declare(strict_types=1);

namespace ITB\ShopwareBoolToYesNoUpdater;

interface LanguagesIdAndNameFetcherInterface
{
    /**
     * @return list<array{id: string, name: string}>
     */
    public function fetchLanguagesIdAndName(): array;
}
